<?php $this->layout("header"); ?>
<div class="container mt51 clearfix row">
	<div class="col-xs-12 col-md-offset-1 col-md-10 mt100">
		<h1>注册</h1>

		<div class="color999">您正在注册本站独立帐号，我们推荐您使用第三方帐号登录</div>
		<?php if(!empty($error_message)): ?>
			<div class="alert alert-danger" style="margin-top: 10px" role="alert"><?php echo $error_message; ?></div>
		<?php endif; ?>

		<form class="register-form" action="" method="post">
			<p>
				<input name="email" class="form-control input-lg" type="email" placeholder="Email地址" required>
			</p>

			<p>
				<input name="password" class="form-control input-lg" type="password" placeholder="登录密码" required>
			</p>

			<p>
				<input name="nickname" class="form-control input-lg" type="text" placeholder="昵称或姓名" required>
			</p>

			<p>
				<input name="code" class="form-control input-lg" type="text" placeholder="请输入图片中的验证码" required
					   maxlength="6"><br/>
				<img src="/index.php/account/captcha">
			</p>

			<div class="align-right">
				<span style="float: left">同意并接受<a href="#">《服务条款》</a></span>
				<input type="submit" class="btn btn-primary btn-lg btn-xl" name="register-submit" value="注册">
			</div>
		</form>
	</div>
</div>
<?php $this->layout("footer");?>