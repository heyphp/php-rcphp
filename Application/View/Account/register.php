<?php $this->layout("header"); ?>
<div class="container mb80 clearfix row">
	<div class="col-xs-12 col-md-offset-1 col-md-10 mt100">
		<h1>ע��</h1>

		<div class="color999">������ע�᱾վ�����ʺţ������Ƽ���ʹ�õ������ʺŵ�¼</div>
		<?php if(!empty($error_message)): ?>
			<div class="alert alert-danger" style="margin-top: 10px;margin-bottom: 10px"
				 role="alert"><?php echo $error_message; ?></div>
		<?php endif; ?>

		<form class="register-form" action="" method="post">
			<div class="register-aside">
				�������������վ�ʺţ���ʹ����Щ�ʺ�<a href="/index.php/account/login">ֱ�ӵ�¼</a>������ע��
				<a style="display: block; margin-top: 6px; text-decoration: none;" href="/index.php/account/login">
					<i class="i-qq">��ѶQQ</i>
					<i class="i-google">Google</i>
					<i class="i-github">Github</i>
					<i class="i-weibo">����΢��</i>
					<i class="i-twitter">Twitter</i>
					<i class="i-facebook">Facebook</i>
					<i class="i-douban">����</i>
				</a>
			</div>
			<p>
				<input name="email" class="form-control input-lg" type="email" placeholder="Email��ַ" required>
			</p>

			<p>
				<input name="password" class="form-control input-lg" type="password" placeholder="��¼����" required>
			</p>

			<p>
				<input name="nickname" class="form-control input-lg" type="text" placeholder="�ǳƻ�����" required>
			</p>

			<p>
				<input name="code" class="form-control input-lg" type="text" placeholder="������ͼƬ�е���֤��" required
					   maxlength="6"><br/>
				<img src="/index.php/account/captcha">
			</p>

			<div class="align-right">
				<span style="float: left">ͬ�Ⲣ����<a href="#">���������</a></span>
				<input type="submit" class="btn btn-primary btn-lg btn-xl" name="register-submit" value="ע��">
			</div>
		</form>
	</div>
</div>
<?php $this->layout("footer"); ?>
