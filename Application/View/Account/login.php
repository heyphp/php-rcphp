<?php $this->layout("header"); ?>
	<div class="container mb80 clearfix row">
		<div class="col-xs-12 col-md-offset-1 col-md-10 mt100">
			<h1>登录</h1>

			<h3 class="slug">最接地气的开发者技术问答社区</h3>

			<div class="color999">您正在注册本站独立帐号，我们推荐您使用第三方帐号登录</div>
			<?php if(!empty($error_message)): ?>
				<div class="alert alert-danger" style="margin-top: 10px;margin-bottom: 10px"
					 role="alert"><?php echo $error_message; ?></div>
			<?php endif; ?>

			<div class="auth-login">
				<p>
					<a class="auth-big" href="/index.php/oauth/google"><i class="i-google-big"></i>Google</a>
					<a class="auth-big" href="/index.php/oauth/github"><i class="i-github-big"></i>GitHub</a>
					<a class="auth-big" href="/index.php/oauth/weibo"><i class="i-weibo-big"></i>新浪微博</a>
					<a class="auth-big" href="/index.php/oauth/qq"><i class="i-qq-big"></i>腾讯QQ</a>
					<a class="more-arrow" href="javascript:;"
					   onclick="$('#more-arrow').removeClass('hidden');$(this).hide();"></a>
				</p>

				<p id="more-arrow" class="hidden">
					<a class="auth-small" href="/index.php/oauth/twitter"><i class="i-twitter"></i>Twitter</a>
					<a class="auth-small" href="/index.php/oauth/facebook"><i class="i-facebook"></i>Facebook</a>
					<a class="auth-small" href="/index.php/oauth/douban"><i class="i-douban"></i>豆瓣</a>
				</p>
			</div>
			<div class="sfid-login">
				或者使用本站帐号登录 ( <a href="/index.php/account/register">注册</a> 或 <a
					href="/index.php/account/forgot">找回密码</a> )
				<form action="" method="post">
					<p>
						<label for="mail" class="hid">Email 地址</label>
						<input class="form-control input-lg" name="email" placeholder="Email 地址" required type="email">
					</p>

					<p>
						<label for="password" class="hid">登录密码</label>
						<input class="form-control input-lg" name="password" placeholder="登录密码" required
							   type="password">
					</p>
					<div class="remember"><input id="remember" name="remember" value="1" checked type="checkbox">
						<label for="remember">下次自动登录</label></div>

					<div class="align-left" style="margin-top: 10px;">
						<input type="submit" class="btn btn-primary btn-lg btn-xl" name="login-submit" value="登录">
					</div>
				</form>
			</div>
		</div>
	</div>
<?php $this->layout("footer"); ?>