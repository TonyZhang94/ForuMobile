<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
	<head>
		<title>For优个人中心</title>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE-edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1" />
		
		<link href="/fuwebapp/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link type="text/css" rel="stylesheet" href="/fuwebapp/Public/css/commonstyle.css" />
		<link type="text/css" rel="stylesheet" href="/fuwebapp/Public/css/style.css" />

		<script type="text/javascript" src="/fuwebapp/Public/script/plugins/jquery-1.11.2.js"></script>

		<script src="/fuwebapp/Public/bootstrap/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div id="login-head">
			<div class="head-top">
				<span class="glyphicon glyphicon-chevron-left fl"></span>
				<span>个人中心</span>
				<span class="fr">注册</span>
			</div>
			<div class="head-bottom">
				<dl>
					<dt>
						<img src="/fuwebapp/Public/img/userhead.png" alt="">
					</dt>
					<dd>
						点击登录
					</dd>
				</dl>
			</div>
		</div>
		<div id="login-input">
		 <!--   <form class="bs-example bs-example-form" role="form">
		      <div class="input-group">
		      	<span input-group-addon > <span class="glyphicon glyphicon-earphone"> </span></span>
		         
		         <input type="text" class="form-control" placeholder="请输入手机号">
		      </div>
			
			  <div class="input-group">
		         <span class="input-group-addon glyphicon glyphicon-lock"> </span>
		         <input type="text" class="form-control" placeholder="请输入密码">
		      </div>
		   </form> -->

		   <form action="">
		   		<div class="user-info-input">
		   			<span class="glyphicon glyphicon-earphone"></span>
		   			<span class="spliter"></span>
		   			<input type="text" placeholder="请输入手机号">
		   		</div>
		   		<div class="user-info-input">
		   			<span class="glyphicon glyphicon-lock"></span>
		   			<span class="spliter"></span>
		   			<input type="text" placeholder="请输入密码">
		   		</div>
		   		<input type="submit" id="login-button" value="登陆">
		   		<div id="login-button-bottom">
		   			<input type="checkbox">
		   			<span>已阅读并同意<a href="">《For优用户服务协议》</a></span>
		   			<span class="fr"><a href="">忘记密码</a></span>
		   		</div>
		   </form>
		</div>
		<div id="common-nav">
	<div class="row">
	   <div class="col-xs-4 active">
	   		<dl>
	   			<dt>
	   				<span class="glyphicon glyphicon-home"></span>
	   			</dt>
	   			<dd>首页</dd>
	   		</dl>
	   </div>
	   <div class="col-xs-4">
	   		<dl>
	   			<dt>
	   				<span class="glyphicon glyphicon-shopping-cart"></span>
	   			</dt>
	   			<dd>购物车</dd>
	   		</dl>
	   </div>
	    <div class="col-xs-4">
	   		<dl>
	   			<dt>
	   				<span class="glyphicon glyphicon-user"></span>
	   			</dt>
	   			<dd>购物车</dd>
	   		</dl>
	   </div>
	</div>
</div>
	</body>
</html>