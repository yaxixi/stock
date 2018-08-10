function selectall()
{
	var m = document.getElementsByName('rid[]');
	for(var i=0;i<m.length;i++)
	{
		m[i].checked = true;
	}
}
function selectother()
{
	var m = document.getElementsByName('rid[]');
	for(var i=0;i<m.length;i++)
	{
		if(m[i].checked == true)
		{
			m[i].checked = false;
		}
		else
		{
			m[i].checked = true;
		}
	}
}
function clearall()
{
	var m = document.getElementsByName('rid[]');
	for(var i=0;i<m.length;i++)
	{
		m[i].checked = false;
	}
}

function mail_read(url)
{
	var m = document.getElementsByName('rid[]');
	for(var i=0;i<m.length;i++)
	{
		if(m[i].checked == true)
		{
			var flag = 1;
		}
	}		
	if(flag != 1)
	{
		alert("请选择要操作的项！");
	}
	else
	{
		document.form1.action = url;
		document.form1.submit();
	}
}

function mail_delete(url)
{
	var m = document.getElementsByName('rid[]');
	for(var i=0;i<m.length;i++)
	{
		if(m[i].checked == true)
		{
			var flag = 1;
		}
	}		
	if(flag != 1)
	{
		alert("请选择要操作的项！");
	}
	else
	{
		if(window.confirm('确定要删除吗？'))
		{
			document.form1.action = url;
			document.form1.submit();
		}		
	}
}