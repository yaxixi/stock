-- stockd.lua
-- Created by yaxixi, 2012.4.10
-- http支付请求模块

L_STOCK_D = "aaa/daemons/stockd";
-- 声明模块名
module("STOCK_D", package.seeall);
declare_module(L_STOCK_D, "STOCK_D");

local timer_id = -1;
local cur_date;

function do_complete_trade()
    local db_name = "xdm406066684_db";

    -- 取得指定时间内完成的交易记录
    local sql_cmd = string.format("select * from trade where sell_time>%d", os.time()-3600*24*45)
    local ret, result_list = DB_D.read_db_crt(db_name, sql_cmd);
    if type(ret) == "string" then
        print("do_complete_trade read trade error : %o\n", ret);
        return;
    end
    local code_map = {};
    if result_list and sizeof(result_list) > 0 then
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
    trace("stock_map :　%o\n", stock_map);
    -- 更新每条交易记录
    local day_map = {};
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
        trace("do_complete_trade sql_list:　%o\n", sql_list);
        DB_D.batch_execute_db_crt(db_name, sql_list);
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
    local day_ret = json_decode(ret);
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
    for _, code in ipairs(code_list) do
        if string.sub(code,0,1) == "6" then
            table.insert(list, "sh" .. code);
        else
            table.insert(list, "sz" .. code);
        end
    end
    local code_str = table.concat(list, ",");
    if sizeof(code_str) == 0 then
        return {};
    end

    local url = "http://hq.sinajs.cn/list=" .. code_str;
    local ret = HTTP_CLIENT_D.get_crt(url);
    -- trace("url : %o, ret : %o", url, ret);
    local stock_map = {};
    for _, code in ipairs(code_list) do
        local str = string.match(ret, code .. "=\"([^\"]*)\"");
        if sizeof(str) > 0 then
            local arr = explode(str, ",");
            stock_map[code] = {
                name = arr[1],
                curr_price = arr[4],
            }
        end
    end

    return stock_map;
end

local function timer_handle()
    local _handler = function()
        do_complete_trade();
    end

    local time_str = os.date("!%H%M", os.time() + 28800);
    if time_str >= "0001" and time_str <= "0003" then
        if cur_date ~= os.date("!%Y%m%d", os.time() + 28800) then
            cur_date = os.date("!%Y%m%d", os.time() + 28800);
        end
    end

    local week = os.date("!%w", os.time() + 28800);
    if (week == "0" or week == "6") then
        return;
    end

    CRT_D.xpcall(_handler);
end

-- 模块的入口执行
function create()

    -- 加载子模块
    timer_id = set_timer(1800000, timer_handle, nil, true)

    cur_date = os.date("!%Y%m%d", os.time() + 28800);
end

create();
