-- stockd.lua
-- Created by yaxixi, 2012.4.10
-- http支付请求模块

L_STOCK_D = "aaa/daemons/stockd";
-- 声明模块名
module("STOCK_D", package.seeall);
declare_module(L_STOCK_D, "STOCK_D");

local timer_id = -1;
local cur_date;
local day_map = {};
local cached_stock_info = {};
local oper_done_date = {};

function do_complete_trade()
    local db_name = "xdm406066684_db";

    if not is_trade_day(os.time(), day_map) then
        return;
    end

    if oper_done_date["do_complete_trade"] == cur_date then
        return;
    end

    -- 取得指定时间内完成的交易记录
    local index, size = 0, 100;
    while true do
        local sql_cmd = string.format("select * from trade where sell_time>%d order by sell_time limit %d,%d", os.time()-3600*24*45, index, size)
        local ret, result_list = DB_D.read_db_crt(db_name, sql_cmd);
        if type(ret) == "string" then
            print("do_complete_trade read trade error : %o\n", ret);
            return;
        end
        local code_map = {};
        local count = sizeof(result_list);
        if count > 0 then
            for _, info in ipairs(result_list) do
                code_map[info.code] = 1;
            end
        end
        local code_list = {};
        for code, _ in pairs(code_map) do
            table.insert(code_list, code);
        end

        -- 取得股票当前价格
        local stock_map = fetch_stock_info(code_list);
        -- trace("stock_map :　%o\n", stock_map);
        -- 更新每条交易记录
        local sql_list = {};
        for _, info in ipairs(result_list) do
            local low_profit = tonumber(info.low_profit);
            local high_profit = tonumber(info.high_profit);
            local sell_time = tonumber(info.sell_time);
            local sell_price = tonumber(info.sell_price);
            local code = info.code;
            if stock_map[code] then
                local curr_price = tonumber(stock_map[code]['curr_price']);
                local profit = (curr_price - sell_price) * 100 / sell_price;
                local sql_cmd = "";
                if profit < 0 then
                    if profit < low_profit and curr_price > 0 then
                        local trade_day = get_trade_day_count(sell_time, os.time(), day_map);
                        sql_cmd = string.format("update trade set low_profit=%s,low_day=%d where id=%s", profit, trade_day, info.id);
                        table.insert(sql_list, sql_cmd);
                    end
                elseif profit > 0 then
                    if profit > high_profit then
                        local trade_day = get_trade_day_count(sell_time, os.time(), day_map);
                        sql_cmd = string.format("update trade set high_profit=%s,high_day=%d where id=%s", profit, trade_day, info.id);
                        table.insert(sql_list, sql_cmd);
                    end
                end
            end
        end
        if #sql_list > 0 then
            -- trace("do_complete_trade sql_list:　%o\n", sql_list);
            DB_D.batch_execute_db_crt(db_name, sql_list);
        end

        if count >= size then
            index = index + size;
        else
            break;
        end
    end

    oper_done_date["do_complete_trade"] = cur_date;
end

function do_month_trade(time)
    local db_name = "xdm406066684_db";

    local date = os.date("!%Y%m%d", time + 28800);
    if oper_done_date["do_month_trade"] == date then
        return;
    end

    local year = tonumber(os.date("!%Y", time + 28800));
    local month = tonumber(os.date("!%m", time + 28800));
    local next_month = month + 1;
    if next_month > 12 then
        year = year + 1;
        next_month = 1;
    end
    local begin_time = ctime(os.date("!%Y%m01000001", time + 28800));
    local end_time = ctime(string.format("%04d%02d01000000", year, next_month));

    -- 取得指定时间内完成的交易记录
    local index, size = 0, 100;
    local user_rows = {};
    while true do
        local sql_cmd = string.format("select * from trade where sell_time>=%d and sell_time<%d order by sell_time limit %d,%d", begin_time, end_time, index, size)
        local ret, result_list = DB_D.read_db_crt(db_name, sql_cmd);
        if type(ret) == "string" then
            print("do_month_trade read trade error : %o\n", ret);
            return;
        end
        local count = sizeof(result_list);
        if count > 0 then
            for _, info in ipairs(result_list) do
                user_rows[info.uid] = user_rows[info.uid] or {};
                table.insert(user_rows[info.uid], info);
            end
        end
        if count >= size then
            index = index + size;
        else
            break;
        end
    end

    for uid, rows in pairs(user_rows) do
        local total_count = #rows;
        local gain_count = 0;
        local total_gain = 0;
        local total_lose = 0;
        local total_gain_money = 0;
        local total_lose_money = 0;
        local total_gain_day = 0;
        local total_lose_day = 0;
        local total_low_profit = 0;
        local total_high_profit = 0;
        local total_money = 0;
        local max_gain = 0;
        local max_lose = 0;
        local min_time = 9999999999;
        local max_time = 0;
        for _, row in ipairs(rows) do
            local profit = tonumber(row.profit);
            local profit_money = tonumber(row.profit_money);
            local buy_price = tonumber(row.buy_price);
            local position = tonumber(row.position);
            total_money = total_money + buy_price * position;
            local buy_time = tonumber(row.buy_time);
            local sell_time = tonumber(row.sell_time);
            local day = math.ceil((sell_time - buy_time) / (3600 * 24));
            total_low_profit = total_low_profit + tonumber(row.low_profit);
            total_high_profit = total_high_profit + tonumber(row.high_profit);
            if profit > 0 then
                gain_count = gain_count + 1;
                total_gain = total_gain + profit;
                total_gain_money = total_gain_money + profit_money;
                max_gain = profit > max_gain and profit or max_gain;
                total_gain_day = total_gain_day + day;
            elseif profit < 0 then
                total_lose = total_lose + profit;
                total_lose_money = total_lose_money + profit_money;
                max_lose = profit < max_lose and profit or max_lose;
                total_lose_day = total_lose_day + day;
            end
        end

        local avg_success = 0;
        local avg_gain = 0;
        local avg_lose = 0;
        local avg_gain_day = 0;
        local avg_lose_day = 0;
        local avg_low_profit = 0;
        local avg_high_profit = 0;
        local profit_money = total_gain_money + total_lose_money;
        local month_str = os.date("!%Y%m", time + 28800);
        total_money = string.format("%.2f", total_money);

        if total_count > 0 then
            avg_success = string.format("%.2f", gain_count * 100 / total_count);
            avg_gain = gain_count == 0 and 0 or string.format("%.3f", total_gain / gain_count);
            avg_lose = gain_count == total_count and 0 or string.format("%.3f", total_lose / (total_count - gain_count));
            avg_gain_day = gain_count == 0 and 0 or math.ceil(total_gain_day / gain_count);
            avg_lose_day = gain_count == total_count and 0 or math.ceil(total_lose_day / (total_count - gain_count));
            avg_low_profit = string.format("%.2f", total_low_profit / total_count);
            avg_high_profit = string.format("%.2f", total_high_profit / total_count);
        end
        local update_cmd = string.format("update trade_month set count=%s,success=%s,gain=%s,lose=%s,profit_money=%s,total_money=%s,max_gain=%s,max_lose=%s,gain_day=%s,lose_day=%s,low_profit=%s,high_profit=%s where month='%s' and uid='%s'", total_count,avg_success,avg_gain,avg_lose,profit_money,total_money,max_gain,max_lose,avg_gain_day,avg_lose_day,avg_low_profit,avg_high_profit,month_str, uid);
        local insert_cmd = string.format("insert into trade_month (month,uid,count,success,gain,lose,profit_money,total_money,max_gain,max_lose,gain_day,lose_day,low_profit,high_profit) values('%s','%s',%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",month_str,uid,total_count,avg_success,avg_gain,avg_lose,profit_money,total_money,max_gain,max_lose,avg_gain_day,avg_lose_day,avg_low_profit,avg_high_profit);
        local ret, result_list = DB_D.replace_db_crt(db_name, { update_cmd, insert_cmd });
        if is_string(ret) or ret < 0 then
            assert(nil, "replace trade_month error!");
        end
    end

    oper_done_date["do_month_trade"] = date;
end

-- 判断指定时间是否为交易日
function is_trade_day(time, day_map)
    day_map = day_map or {};
    local date = os.date("!%Y%m%d", time + 28800);
    if day_map[date] then
        if day_map[date] == 0 then
            return true;
        else
            return false;
        end
    end

    local list = { date };
    local day_str = table.concat(list, ",") .. ",";
    local url = "http://tool.bitefu.net/jiari/?d=" .. day_str;
    local ret = HTTP_CLIENT_D.get_crt(url);
    local day_ret = ret and json_decode(ret);
    if not day_ret then
        print("取交易日天数时解析返回值失败。");
        return;
    end
    day_map[date] = tonumber(day_ret[date]);
    if day_map[date] == 0 then
        return true;
    else
        return false;
    end
end

-- 取得指定时间内的交易日天数
function get_trade_day_count(begin_time, end_time, day_map)
    local day = 0;
    local list = {};
    day_map = day_map or {};
    while begin_time < end_time do
        local begin_date = os.date("!%Y%m%d", begin_time + 28800);
        local end_date = os.date("!%Y%m%d", end_time + 28800);
        if begin_date == end_date then
            break;
        end

        begin_time = begin_time + 3600 * 24;
        local date = os.date("!%Y%m%d", begin_time + 28800);
        if day_map[date] then
            if day_map[date] == 0 then
                day = day + 1;
            end
        else
            table.insert(list, date);
        end
    end

    if #list == 0 then
        return day;
    end

    local day_str = table.concat(list, ",") .. ",";
    local url = "http://tool.bitefu.net/jiari/?d=" .. day_str;
    local ret = HTTP_CLIENT_D.get_crt(url);
    local day_ret = ret and json_decode(ret);
    if not day_ret then
        print("取交易日天数时解析返回值失败。");
        return;
    end
    --trace("day url : %o, day_ret : %o\n", url, day_ret);
    for date, value in pairs(day_ret) do
        day_map[date] = tonumber(value);
        if tonumber(value) == 0 then
            day = day + 1;
        end
    end

    return day;
end

-- 取得股票信息
function fetch_stock_info(code_list)
    local list = {};
    local stock_map = {};
    for _, code in ipairs(code_list) do
        local flag = false;
        if cached_stock_info[code] then
            local cache_time = cached_stock_info[code]["time"];
            local hour = tonumber(os.date("!%H", os.time() + 28800));
            if (os.time() - cache_time <= 600) then
                flag = true;
            else
                local trade_day = is_trade_day(os.time(), day_map);
                if not trade_day then
                    flag = true;
                elseif hour < 9 or hour > 16 then
                    flag = true;
                end
            end
            if flag then
                stock_map[code] = {
                    name = cached_stock_info[code]["name"],
                    curr_price = cached_stock_info[code]["curr_price"],
                };
            end
        end

        if not flag then
            if string.sub(code,0,1) == "6" then
                table.insert(list, "sh" .. code);
            else
                table.insert(list, "sz" .. code);
            end
        end
    end
    local code_str = table.concat(list, ",");
    if sizeof(code_str) == 0 then
        return stock_map;
    end

    local url = "http://hq.sinajs.cn/list=" .. code_str;
    local ret = HTTP_CLIENT_D.get_crt(url);
    if sizeof(ret) == 0 then
        assert(nil, "fetch_stock_info fetch url error!");
        return stock_map;
    end
    -- trace("url : %o, ret : %o", url, ret);
    for _, code in ipairs(code_list) do
        local str = string.match(ret, code .. "=\"([^\"]*)\"");
        if sizeof(str) > 0 then
            local arr = explode(str, ",");
            stock_map[code] = {
                name = arr[1],
                curr_price = arr[4],
            }
            cached_stock_info[code] = {
                name = arr[1],
                curr_price = arr[4],
                time = os.time(),
            }
        else
            assert(nil, string.format("fetch_stock_info fetch code(%s) error!", code));
        end
    end

    return stock_map;
end

local function timer_handle()
    if cur_date ~= os.date("!%Y%m%d", os.time() + 28800) then
        cur_date = os.date("!%Y%m%d", os.time() + 28800);
    end

    local hour = tonumber(os.date("!%H", os.time() + 28800));
    local _do_complete_trade = function()
        do_complete_trade();
    end
    local _do_month_trade = function()
        if not is_trade_day(os.time(), day_map) then
            return;
        end
        do_month_trade(os.time());
    end
    if hour >= 15 and hour <= 16 then
        CRT_D.xpcall(_do_complete_trade);
    end
    if hour >= 17 and hour <= 18 then
        CRT_D.xpcall(_do_month_trade);
    end
end

function destruct()
    if timer_id ~= -1 then
        delete_timer(timer_id);
        timer_id = -1;
    end
end

-- 模块的入口执行
function create()

    -- 加载子模块
    timer_id = set_timer(1800000, timer_handle, nil, true)

    cur_date = os.date("!%Y%m%d", os.time() + 28800);
end

create();
