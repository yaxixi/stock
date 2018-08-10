//post
//功能介绍：post请求
function post(URL, PARAMS) {
  var temp = document.createElement("form");
  temp.action = URL;
  temp.method = "post";
  temp.style.display = "none";
  for (var x in PARAMS) {
    var opt = document.createElement("textarea");
    opt.name = x;
    opt.value = PARAMS[x];
    // alert(opt.name)
    temp.appendChild(opt);
  }
  document.body.appendChild(temp);
  temp.submit();
  return temp;
}

//函数名：CheckDateTime
//功能介绍：检查是否为日期时间
function checkDateTime(str){
  var reg = /^(\d+)-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2})$/;
  var r = str.match(reg);
  if(r==null) return false;
  r[2]=r[2]-1;
  var d= new Date(r[1], r[2],r[3], r[4],r[5], 0);
  if(d.getFullYear()!=r[1])return false;
  if(d.getMonth()!=r[2])return false;
  if(d.getDate()!=r[3])return false;
  if(d.getHours()!=r[4])return false;
  if(d.getMinutes()!=r[5])return false;
  return true;
}

function pad(num, n) {
    var len = num.toString().length;
    while(len < n) {
        num = "0" + num;
        len++;
    }
    return num;
}

// 传入date对象，返回"20170410133000"字符串
function get_date_format(d) {
  var year = d.getFullYear();
  var month = d.getMonth() + 1;
  var day = d.getDate();
  var hour = d.getHours();
  var min = d.getMinutes();
  var sec = d.getSeconds();

  return "" + year + pad(month, 2) + pad(day, 2)
    + pad(hour, 2) + pad(min, 2) + pad(sec, 2);
}

// 判断是否是数字
function is_number(value) {
    var patrn = /^(-)?\d+(\.\d+)?$/;
    if (patrn.exec(value) == null || value == "") {
        return false
    } else {
        return true
    }
}