<?php $this->layout("header"); ?>
	<div class="container mb80 clearfix row mt100">
		<div class="col-xs-12">
			<h2 class="common-title">我的个人资料</h2>
		</div>
		<div class="col-md-3 col-md-push-9 fs13">
			<nav class="navlist-group navlist-group-right">
				<a class="navlist-group-item active" href="/index.php/account/settings">我的个人资料</a>
				<a class="navlist-group-item" href="/index.php/account/settings">我的头像</a>
				<a class="navlist-group-item" href="/index.php/account/settings">Email 地址</a>
				<a class="navlist-group-item" href="/index.php/account/settings">密码和绑定帐号</a>
			</nav>
		</div>

		<div id="main" class="settings col-md-9 col-md-pull-3">
			<form action="" method="post" class="session-form">
				<p>
					<label for="nickname" class="required">称呼</label>
					<input name="nickname" id="nickname" type="text" maxlength="32" placeholder="常用昵称或真实姓名"
						   class="form-control text-32" value="<?php echo $user['nickname']; ?>" required>
				</p>

				<p class="fix-size">
					<label>性别</label>
					<input name="gender"<?php echo $user['gender'] == 0 ? ' checked' : ''; ?> type="radio" id="secret"
						   value="0"> <label for="secret">保密</label> &nbsp;&nbsp;
					<input name="gender"<?php echo $user['gender'] == 1 ? ' checked' : ''; ?> type="radio" id="male"
						   value="1"> <label for="male">男</label> &nbsp;&nbsp;
					<input name="gender"<?php echo $user['gender'] == 2 ? ' checked' : ''; ?> type="radio" id="female"
						   value="2"> <label for="female">女</label>
				</p>

				<p>
					<label for="birthday">生日</label>
					<input name="birthday" id="birthday" type="date" placeholder="格式 YYYY-MM-DD"
						   value="<?php echo $user['birthday']; ?>" class="form-control text-32">
				</p>

				<p>
					<label for="mobile">电话</label>
					<input name="mobile" id="mobile" type="tel" placeholder="电话"
						   value="<?php echo $user['mobile']; ?>" class="form-control text-32">
				</p>

				<div class="p">
					<label for="address">通讯地址</label>
					<div>
						<input name="address" id="address" type="text" maxlength="32" placeholder="详细通信地址"
							   class="form-control text-32" value="<?php echo $user['address']; ?>">
					</div>
				</div>

				<div class="p">
					<label for="company">公司</label>
					<div>
						<input name="company" id="company" type="text" maxlength="32" placeholder="公司名称"
							   class="form-control text-32" value="<?php echo $user['company']; ?>">
					</div>
				</div>

				<div class="p">
					<label for="job">我的工作是</label>
					<div>
						<select class="form-control" id="job" name="job">
							<option value="0">请选择</option>
							<?php foreach($jobs as $k => $v):?>
							<option value="<?php echo $k;?>"<?php echo $k == $user['job'] ? ' selected' : '';?>><?php echo $v;?></option>
							<?php endforeach;?>
						</select>
					</div>
				</div>

				<p>
					<label for="homepage">个人网站</label>
					<input name="homepage" id="homepage" type="url" placeholder="http://example.com"
						   value="<?php echo $user['homepage']; ?>" class="form-control mono text-32">
				</p>

				<p>
					<label for="description">自我简介</label>
					<textarea name="description" id="description" class="form-control textarea-14"
							  rows="4"><?php echo $user['description']; ?></textarea>
				</p>

				<div class="form-action">
					<input type="submit" name="settings-profile-submit" class="btn btn-xl btn-primary" value="提交">
				</div>
			</form>


		</div>
	</div>
<?php $this->layout("footer"); ?>