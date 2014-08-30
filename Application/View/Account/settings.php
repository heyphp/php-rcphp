<?php $this->layout("header"); ?>
	<div class="container mb80 clearfix row mt100">
		<div class="col-xs-12">
			<h2 class="common-title">�ҵĸ�������</h2>
		</div>
		<div class="col-md-3 col-md-push-9 fs13">
			<nav class="navlist-group navlist-group-right">
				<a class="navlist-group-item active" href="/index.php/account/settings">�ҵĸ�������</a>
				<a class="navlist-group-item" href="/index.php/account/settings">�ҵ�ͷ��</a>
				<a class="navlist-group-item" href="/index.php/account/settings">Email ��ַ</a>
				<a class="navlist-group-item" href="/index.php/account/settings">����Ͱ��ʺ�</a>
			</nav>
		</div>

		<div id="main" class="settings col-md-9 col-md-pull-3">
			<form action="" method="post" class="session-form">
				<p>
					<label for="nickname" class="required">�ƺ�</label>
					<input name="nickname" id="nickname" type="text" maxlength="32" placeholder="�����ǳƻ���ʵ����"
						   class="form-control text-32" value="<?php echo $user['nickname']; ?>" required>
				</p>

				<p class="fix-size">
					<label>�Ա�</label>
					<input name="gender"<?php echo $user['gender'] == 0 ? ' checked' : ''; ?> type="radio" id="secret"
						   value="0"> <label for="secret">����</label> &nbsp;&nbsp;
					<input name="gender"<?php echo $user['gender'] == 1 ? ' checked' : ''; ?> type="radio" id="male"
						   value="1"> <label for="male">��</label> &nbsp;&nbsp;
					<input name="gender"<?php echo $user['gender'] == 2 ? ' checked' : ''; ?> type="radio" id="female"
						   value="2"> <label for="female">Ů</label>
				</p>

				<p>
					<label for="birthday">����</label>
					<input name="birthday" id="birthday" type="date" placeholder="��ʽ YYYY-MM-DD"
						   value="<?php echo $user['birthday']; ?>" class="form-control text-32">
				</p>

				<p>
					<label for="mobile">�绰</label>
					<input name="mobile" id="mobile" type="tel" placeholder="�绰"
						   value="<?php echo $user['mobile']; ?>" class="form-control text-32">
				</p>

				<div class="p">
					<label for="address">ͨѶ��ַ</label>
					<div>
						<input name="address" id="address" type="text" maxlength="32" placeholder="��ϸͨ�ŵ�ַ"
							   class="form-control text-32" value="<?php echo $user['address']; ?>">
					</div>
				</div>

				<div class="p">
					<label for="company">��˾</label>
					<div>
						<input name="company" id="company" type="text" maxlength="32" placeholder="��˾����"
							   class="form-control text-32" value="<?php echo $user['company']; ?>">
					</div>
				</div>

				<div class="p">
					<label for="job">�ҵĹ�����</label>
					<div>
						<select class="form-control" id="job" name="job">
							<option value="0">��ѡ��</option>
							<?php foreach($jobs as $k => $v):?>
							<option value="<?php echo $k;?>"<?php echo $k == $user['job'] ? ' selected' : '';?>><?php echo $v;?></option>
							<?php endforeach;?>
						</select>
					</div>
				</div>

				<p>
					<label for="homepage">������վ</label>
					<input name="homepage" id="homepage" type="url" placeholder="http://example.com"
						   value="<?php echo $user['homepage']; ?>" class="form-control mono text-32">
				</p>

				<p>
					<label for="description">���Ҽ��</label>
					<textarea name="description" id="description" class="form-control textarea-14"
							  rows="4"><?php echo $user['description']; ?></textarea>
				</p>

				<div class="form-action">
					<input type="submit" name="settings-profile-submit" class="btn btn-xl btn-primary" value="�ύ">
				</div>
			</form>


		</div>
	</div>
<?php $this->layout("footer"); ?>