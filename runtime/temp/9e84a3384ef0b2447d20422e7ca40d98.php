<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:97:"D:\UPUPW_AP5.4-1510\UPUPW_AP5.4\htdocs\wochuang\public/../application/index\view\admin\login.html";i:1492682417;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<script>

	</script>
	<style>
		body{
			font-family: '微软雅黑';
		}
		section{
			position: fixed;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			background-color: #363636;
			/* background-image: url('a') */
		}
		.center{
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			border-radius: 5px;
			background-color: #363636;
		}
		.center-in{
			background-color: #1283C9;
			width: 499px;
			height: 206px;
			margin:150px auto;
		}
		.up-word{
			color:white;
			font-size: 30px;
			box-sizing: border-box;
			padding:20px 20px;
		}
		.up-word span:nth-child(3){
			font-size: 20px;
		}
		.infor{
			width:350px;
			/* padding: 0 20px; */
			box-sizing: border-box;
			position: relative;
			font-size: 0;
		}
		.infor input{
			/* margin-left: 10px; */
			width: 294px;
			height: 30px;
			background-color: #2A2A2A;
			border: none;
			color: #fff;
			font-size: 13px;
			box-sizing: border-box;
			padding-left: 10px;
			box-shadow: inset 0 0px 4px 0 rgba(0, 0, 0, 0.5);\
		border-radius: 6px !important;
		}
		.infor span {
			color:white;
			display: inline-block;
			color: #8C8C8C;
			padding: 0 10px;
			font-size: 14px;
		}
		.infor p {
			margin-top:8px;
			width: 350px;
		}
		.infor div{
			position: absolute;
			top: 50%;
			right: 0;
			transform: translate(50%, -50%);
			width: 40px;
			height: 40px;
			box-sizing: border-box;
			padding:6px;
			background-color: #363636;
			border-radius: 50%;
			/* box-shadow:  0 0px 4px 0 rgba(0, 0, 0, 0.5); */
		}
		.infor button{
			position: absolute;
			top: 50%;
			/* left: 7px; */
			transform: translate(0, -50%);
			width: 30px;
			height: 30px;
			padding: 0;
			outline: none;
			background-color: #E94241;
			border-radius: 50%;
			color: #fff;
			border: none;
			cursor: pointer;
			/* margin-left: 100px;
            margin-top: 10px; */
		}
		#app .center .infor p .id , #app .center .infor p .psw{
			outline: none;
			border-radius: 6px;
		}
		button:hover{
			background-color: #07A9F5;
			color: #fff;
		}
	</style>
	<link href="__ROOT__/admin/css/app.9f1ac770f48386361a0dfc90d01d8b7a.css" rel="stylesheet"></head>
<body>
<section id="app">
	<div class="center">
		<!-- <div class="up-word">
            <span>管理登录</span>
            <span>|</span>
            <span>AdminLogin</span>
        </div> -->
		<div class="infor">
			<p><span>账号 :</span><input class="id" type="text" id="name" placeholder="请输入管理员账号"></p>
			<p><span>密码 :</span><input class="psw" type="password" id="keyword" placeholder="请输入管理员密码"></p>
			<div class="btn-wrap"><button id="btn" class="fin-search">Go</button></div>
		</div>
	</div>
</section>
<script type="text/javascript" src="__ROOT__/admin/js/manifest.dc6351f267bc73933664.js"></script><script type="text/javascript" src="__ROOT__/admin/js/app.4ef540025aed9ccbea94.js"></script></body>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script >
	window.onload=function(){

		jQuery("#btn").on("click",function(){
			var username = jQuery("#name").val()
			var ip = "http://47.93.52.144";

			var arr={
				username:jQuery("#name").val(),
				password:jQuery("#keyword").val()
			}
			jQuery.ajax({
				type: 'POST',
				data: arr,
				url: ip+"/index/Managercontroller/doLogin",
				dataType: 'json',
				success:function(resp){
					if (resp.code == "2004") {
						alert("用户名或者密码错误");
						jQuery("#keyword").val('');
						jQuery("#name").val('');
						return
					}
					else{
						window.localStorage.setItem('adminMassge',resp.data)
						window.location.href="/index/admin";
					}
				}
			})

		})
		// 键盘点击事件
		document.onkeydown = function (e) {
			var ip = "http://127.0.0.1";
			var e = event || window.event;
			var keyCode = e.keyCode || e.which;
			if (keyCode == 13) {
				var arr={
					username:jQuery("#name").val(),
					password:jQuery("#keyword").val()
				}
				jQuery.ajax({
					type:'POST',
					data:arr,
					url:ip+"/wochuang/public/index/Managercontroller/doLogin",
					dataType: 'json',
					success:function(resp){
						if (resp.code == "2004") {
							alert("用户名或者密码错误");
							jQuery("#keyword").val('');
							jQuery("#name").val('');
							return
						}
						else{
							window.localStorage.setItem('adminMassge',resp.data)
							window.location.href="__ROOT__/index/admin";
						}
					}
				})
			}
		}
	}
</script>
</html>