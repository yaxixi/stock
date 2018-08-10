$(document).ready(function(){
	detect();
	detect_exception();
	detect_vip_eshop();
});

function detect() {
	$.ajax({
		type:"post",
		url:"../tongji/webroot/index.php",
		data:"s=detect",
		cache:false,
		dataType:"json",
		success:function(msg){
			if (msg['key'] == 1) {
				if (!time_old) {//初始化
					time_old = time_new = msg['time'];
					num_online_old = num_online_new = msg['num_online'];
					num_login_old = num_login_new = msg['num_login'];
					num_new_old = num_new_new = msg['num_new'];
				} else {
					if (msg['time'] != time_new) {
						time_old = time_new;
						time_new = msg['time'];
						num_online_old = num_online_new;
						num_online_new = msg['num_online'];
						num_login_old = num_login_new;
						num_login_new = msg['num_login'];
						num_new_old = num_new_new;
						num_new_new = msg['num_new'];
						var str = '';
						var num_online_new_p = num_online_new/num_online_old;
						var num_login_new_p = num_login_new/num_login_old;
						var num_new_new_p = num_new_new/num_new_old;
						if (num_online_new_p < 0.7) {
							str += msg['time']+'，在线：'+num_online_new+'，为'+num_online_old+'的'+num_online_new_p+'\r\n';
						}
						if (num_login_new_p < 0.6) {
							str += msg['time']+'，登录：'+num_login_new+'，为'+num_login_old+'的'+num_login_new_p+'\r\n';
						}
						if (num_new_new_p < 0.6) {
							str += msg['time']+'，新增：'+num_new_new+'，为'+num_new_old+'的'+num_new_new_p+'\r\n';
						}
						if (str) {
							alert('XXXX：\r\n'+str);
						}
					}
				}
			}
		}
	});
}

function detect_exception() {
	$.ajax({
		type:"post",
		url:"../tongji/webroot/index.php",
		data:"s=detect&ac=detect_exception",
		cache:false,
		dataType:"json",
		success:function(msg){
			if (msg['key'] == 1) {
				if (!exception_id) {//初始化
					exception_id = msg['id'];
				} else {
					if (msg['id'] > exception_id) {
						exception_id = msg['id'];
						alert('发现外挂异常账号');
					}
				}
			}
		}
	});
}

function detect_vip_eshop() {
	$.ajax({
		type:"post",
		url:"../tongji/webroot/index.php",
		data:"s=detect&ac=detect_vip_eshop",
		cache:false,
		dataType:"json",
		success:function(msg){
			if (msg['key'] == 1) {
				if (!hour_old) {//初始化
					hour_old = hour_new = msg['time'];
					num_vip_old = num_vip_new = msg['num_vip'];
					num_eshop_old = num_eshop_new = msg['num_eshop'];
				} else {
					if (msg['time'] != hour_new) {
						hour_old = hour_new;
						hour_new = msg['time'];
						num_vip_old = num_vip_new;
						num_vip_new = msg['num_vip'];
						num_eshop_old = num_eshop_new;
						num_eshop_new = msg['num_eshop'];
						var str = '';
						if (num_vip_old == num_vip_new) {
							str += 'VIP累积收入报警\r\n';
						}
						if (num_eshop_old == num_eshop_new) {
							str += '商城累积收入报警\r\n';
						}
						if (str) {
							alert('YYYY\r\n'+str);
						}
					}
				}
			}
		}
	});
}