/*
 CNZZ JS INIT FUNCTIONS
*/
$(document).ready(function(){
		$('#date_search').click(function(){
			date_search($('#date_from').val(),$('#date_to').val());
	         }
		);
		$('#date_search_c').click(function(){
                        date_search_c($('#date_from').val(),$('#date_to').val());
             }
        );
		if($('.click_change').length > 0){
			$('.click_change').each(function(i){
				$(this).click(function(){
					click_change(this.title);
				});
			});
		}
	}
);

function date_search_c(from,to){
        if(to.length==0 || from.length==0){
                return;
        }
		if(to==from){
				alert("您不能选择同一天进行对比，请重试");
				return;
		}
        if(compare_date_c(from,to)){
                location.href='/v1/main.php?siteid='+_siteid+'&s='+_s+'&day_from='+from+'&day_to='+to;
        }
}

function date_search(from,to){
	if(to.length==0 || from.length==0){
		return;
	}
	if(compare_date(from,to)){
		location.href='/main.php?s='+_s+'&st='+from+'&et='+to;
	}
}

function click_change(stitle){
	var titles=['本月每日','今日每小时','实况每分钟'];
	for(var i=0;i<titles.length;i++){
		if(stitle==titles[i]){
			$('[title='+stitle+']').attr({id:'current'});
			if(stitle=='今日每小时')get_today_hour_flux();
			if(stitle=='实况每分钟')get_live_minutes();
			if(stitle=='本月每日')get_month_pv();
		}else{
			$('[title='+titles[i]+']').attr({id:''});
		}
	}
}

/*
  今日每小时流量。用于播报页面。
*/
function get_today_hour_flux(sData){
		var so = new SWFObject("./flash/online.swf", "charts", "650", "190", "8", "#FFFFFF");
		so.addVariable("path", "./charts/");
		so.addVariable("settings_file", escape("./xml/online_settings.xml"));
		if(typeof sData=='undefined')
			so.addVariable("data_file", escape("./data/online_data.php?webid="+_siteid+"&s=pv"));
		else{
			so.addVariable("data_file", escape("./data/online_data.php?"+sData));

		}
		so.addVariable("preloader_color", "#999999");
		so.write("flashcontent");
		if(typeof sData=='undefined'){
			changeLiveBobaoFlashInit();
			$('#bobao_pv').click(function(){changeLiveBobaoFlash(1)});
			$('#bobao_uv').click(function(){changeLiveBobaoFlash(1)});
			$('#bobao_ip').click(function(){changeLiveBobaoFlash(1)});
		}
		$('#show_user_sel').show();
}
/*
 实况每分钟。用于播报页面。
*/
function get_live_minutes(){
	var so = new SWFObject("./flash/live_bobao_min.swf", "amcolumn", "600", "188", "0", "#FFFFFF");
	so.addVariable("path", "./charts/");
	so.addVariable("settings_file", escape("./xml/live_bobao_min_setting.xml")); 
	so.addVariable("data_file", escape("./data/live_bobao_min_data.php?webid="+_siteid));		
	so.addVariable("preloader_color", "#999999");	
	so.write("flashcontent");
	$('#show_user_sel').hide();
}

/*
本月每日。用于播报页面。
*/
function get_month_pv(sData){
		var so = new SWFObject("./flash/online.swf", "amcolumn", "600", "188", "1", "#FFFFFF");
		so.addVariable("path", "./charts/");
		so.addVariable("settings_file", escape("./xml/online_settings.xml"));
		if(typeof sData=='undefined')
			so.addVariable("data_file", escape("./data/live_bobao_month.php?webid="+_siteid+"&s=pv"));
		else
			so.addVariable("data_file", escape("./data/live_bobao_month.php?"+sData));
    		so.addVariable("preloader_color", "#999999");	
		so.write("flashcontent");
		if(typeof sData=='undefined'){
			changeLiveBobaoFlashInit();
			$('#bobao_pv').click(function(){changeLiveBobaoFlash(2)});
			$('#bobao_uv').click(function(){changeLiveBobaoFlash(2)});
			$('#bobao_ip').click(function(){changeLiveBobaoFlash(2)});
		}
		$('#show_user_sel').show();
}

function changeLiveBobaoFlash(type){
	var tys = new Array();
	if($('#bobao_pv').attr('checked') == true)
		tys[tys.length] = 'pv';
	if($('#bobao_ip').attr('checked') == true)
		tys[tys.length] = 'ip';
	if($('#bobao_uv').attr('checked') == true)
		tys[tys.length] = 'uv';
	if(tys.length == 0){
		alert("你必须在PV,IP,UV中至少选择一项！");
		return;
	}
	var s = tys.join();
	XMLHttp.sendReq('GET', 'data/online_flash.inc.php?webid='+_siteid+'&s='+s,'',function(o){
						sData=(o.responseText)
						if(type==1)
							get_today_hour_flux(sData);
						else
							get_month_pv(sData);
						
							}
			); 
}

function changeLiveBobaoFlashInit(){
	$('#bobao_pv').unbind('click');
	$('#bobao_uv').unbind('click');
	$('#bobao_ip').unbind('click');

	$('#bobao_uv').attr({checked:false});
	$('#bobao_ip').attr({checked:false});
}

function compare_date(a,b){
	var pattern = /^\d{4}-\d{2}-\d{2}$/;
	var arr=a.split("-");
	var starttime=new Date(arr[0],arr[1],arr[2]);
	var starttimes=starttime.getTime();
	var arrs=b.split("-");
	var lktime=new Date(arrs[0],arrs[1],arrs[2]);
	var lktimes=lktime.getTime();

	var _arrs=_add_stat_time.split("-");
	var _lktime=new Date(_arrs[0],_arrs[1],_arrs[2]);
	var _add_stat_time2=_lktime.getTime();

	var t = new Date;
	var r={};
	r.year  = t.getFullYear();
	r.month = t.getMonth() + 1;
	r.day   = t.getDate();
	var today = new Date(r.year,r.month,r.day);
	today = today.getTime();
        if(pattern.test(a)==false){
                alert("日期输入有误，请重新输入");
                document.getElementById('date_from').focus();
                return false;
        }else if(pattern.test(b)==false){
                alert("日期输入有误，请重新输入");
                document.getElementById('date_to').focus();
                return false;
	}else if(starttimes>lktimes){
		alert('日期输入错误。');
		document.getElementById('date_from').value=_st;
		document.getElementById('date_to').value=_et;
		return false;
	}else if(starttimes < _add_stat_time2){
		alert('开始时间不能早于统计开通时间！');
		document.getElementById('date_from').value=_st;
		document.getElementById('date_to').value=_et;
		return false;
	}else if(today < starttimes){
		alert('日期输入错误。');
		document.getElementById('date_from').value=_st;
		document.getElementById('date_to').value=_et;
		return false;
	}else if(today < lktime){
		alert('日期输入错误。');
		document.getElementById('date_from').value=_st;
		document.getElementById('date_to').value=_et;
		return false;
        }
	return true;
}

function compare_date_c(a,b){
        var pattern = /^\d{4}-\d{2}-\d{2}$/;
	var arr=a.split("-");
	var starttime=new Date(arr[0],arr[1],arr[2]);
	var starttimes=starttime.getTime();
	var arrs=b.split("-");
	var lktime=new Date(arrs[0],arrs[1],arrs[2]);
	var lktimes=lktime.getTime();

	var _arrs=_add_stat_time.split("-");
	var _lktime=new Date(_arrs[0],_arrs[1],_arrs[2]);
	var _add_stat_time2=_lktime.getTime();

	var t = new Date;
	var r={};
	r.year  = t.getFullYear();
	r.month = t.getMonth() + 1;
	r.day   = t.getDate();
	var today = new Date(r.year,r.month,r.day);
	today = today.getTime();
        if(pattern.test(a)==false){
                alert("日期输入有误，请重新输入");
                document.getElementById('date_from').focus();
                return false;
        }else if(pattern.test(b)==false){
                alert("日期输入有误，请重新输入");
                document.getElementById('date_to').focus();
                return false;
	}else if(starttimes>lktimes){
		alert('日期输入错误。');
		document.getElementById('date_from').value=_st;
		document.getElementById('date_to').value=_et;
		return false;
	}else if(starttimes < _add_stat_time2){
		alert('日期输入错误。');
		document.getElementById('date_from').value=_st;
		document.getElementById('date_to').value=_et;
		return false;
	}else if(today <= starttimes){
		alert('日期输入错误。');
		document.getElementById('date_from').value=_st;
		document.getElementById('date_to').value=_et;
		return false;
	}else if(today <= lktime){
		alert('日期输入错误。');
		document.getElementById('date_from').value=_st;
		document.getElementById('date_to').value=_et;
		return false;
	}
	return true;
}

