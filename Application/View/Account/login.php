<?php $this->layout("header"); ?>
	<div class="container mb80 clearfix row">
		<div class="col-xs-12 col-md-offset-1 col-md-10 mt100">
			<h1>��¼</h1>

			<h3 class="slug">��ӵ����Ŀ����߼����ʴ�����</h3>

			<div class="color999">������ע�᱾վ�����ʺţ������Ƽ���ʹ�õ������ʺŵ�¼</div>
			<?php if(!empty($error_message)): ?>
				<div class="alert alert-danger" style="margin-top: 10px;margin-bottom: 10px"
					 role="alert"><?php echo $error_message; ?></div>
			<?php endif; ?>

			<div class="auth-login">
				<p>
					<a class="auth-big" href="/index.php/oauth/google"><i class="i-google-big"></i>Google</a>
					<a class="auth-big" href="/index.php/oauth/github"><i class="i-github-big"></i>GitHub</a>
					<a class="auth-big" href="/index.php/oauth/weibo"><i class="i-weibo-big"></i>����΢��</a>
					<a class="auth-big" href="/index.php/oauth/qq"><i class="i-qq-big"></i>��ѶQQ</a>
					<a class="more-arrow" href="javascript:;"
					   onclick="$('#more-arrow').removeClass('hidden');$(this).hide();"></a>
				</p>

				<p id="more-arrow" class="hidden">
					<a class="auth-small" href="/index.php/oauth/twitter"><i class="i-twitter"></i>Twitter</a>
					<a class="auth-small" href="/index.php/oauth/facebook"><i class="i-facebook"></i>Facebook</a>
					<a class="auth-small" href="/index.php/oauth/douban"><i class="i-douban"></i>����</a>
				</p>
			</div>
			<div class="sfid-login">
				����ʹ�ñ�վ�ʺŵ�¼ ( <a href="/index.php/account/register">ע��</a> �� <a
					href="/index.php/account/forgot">�һ�����</a> )
				<form action="" method="post">
					<p>
						<label for="mail" class="hid">Email ��ַ</label>
						<input class="form-control input-lg" name="email" placeholder="Email ��ַ" required type="email">
					</p>

					<p>
						<label for="password" class="hid">��¼����</label>
						<input class="form-control input-lg" name="password" placeholder="��¼����" required
							   type="password">
					</p>
					<div class="remember"><input id="remember" name="remember" value="1" checked type="checkbox">
						<label for="remember">�´��Զ���¼</label></div>

					<div class="align-left" style="margin-top: 10px;">
						<input type="submit" class="btn btn-primary btn-lg btn-xl" name="login-submit" value="��¼">
					</div>
				</form>
			</div>
		</div>
	</div>
<?php $this->layout("footer"); ?>