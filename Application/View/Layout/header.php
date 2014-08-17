<!DOCTYPE HTML>
<html>
<head>
	<meta charset="gb2312">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="/Public/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/Public/Style/global.css">
</head>
<body id="xtopjsinfo">
<!-- Fixed navbar -->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-collapse collapse">
			<form class="navbar-form navbar-left">
				<input type="text" class="form-control rz-top-search-input" placeholder="请输入您要搜索的问题……">
				<button type="button" class="rz-top-add-question">+ 提问</button>
			</form>
			<ul class="nav navbar-nav">
				<li><a href="/">首页</a></li>
				<li class="current"><a href="/index.php/find">发现</a></li>
				<li><a href="/index.php/subject">话题</a></li>
				<li><a href="/index.php/tags">标签</a></li>
				<li><a href="/index.php/users">用户</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="/index.php/account/register">注册</a></li>
				<li><a href="/index.php/account/login">登录</a></li>
			</ul>
		</div>
		<!--/.nav-collapse -->
	</div>
</div>