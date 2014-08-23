<footer class="footer">
	<div class="container">
		<div class="row inner hidden-xs">
			<dl class="col-sm-2 site-link">
				<dt>网站相关</dt>
				<dd><a href="#">关于我们</a></dd>
				<dd><a href="#">服务条款</a></dd>
				<dd><a href="#">帮助中心</a></dd>
				<dd><a href="#">编辑器语法</a></dd>
				<dd><a href="#">每周精选</a></dd>
			</dl>
			<dl class="col-sm-2 site-link">
				<dt>联系合作</dt>
				<dd><a href="#">加入我们</a></dd>
				<dd><a href="#">合作伙伴</a></dd>
				<dd><a href="#">媒体报道</a></dd>
				<dd><a href="#">建议反馈</a></dd>
			</dl>
			<dl class="col-sm-2 site-link">
				<dt>常用链接</dt>
				<dd><a href="#" target="_blank">开发日志</a></dd>
			</dl>
			<dl class="col-sm-2 site-link">
				<dt>关注我们</dt>
				<dd><a href="http://weibo.com/phperweb" target="_blank">新浪微博</a></dd>
				<dd><a href="http://twitter.com/380176861" target="_blank">Twitter</a></dd>
			</dl>
			<dl class="col-sm-4 site-link" id="license">
				<dt>内容许可</dt>
				<dd>除特别说明外，用户内容均采用 <a rel="license" target="_blank"
									  href="http://creativecommons.org/licenses/by-sa/3.0/cn/">知识共享署名-相同方式共享 3.0
						中国大陆许可协议</a> 进行许可
				</dd>
				<dd>本站由 <a target="_blank" href="http://coding.net/">Coding.Net</a> 提供 项目部署</dd>
			</dl>
		</div>
		<div class="copyright">
			Copyright &copy; 2014 Renzhi. 当前呈现版本 <?php echo APP_VERSION; ?>
		</div>
	</div>
</footer>
<script type="text/javascript" src="/Public/Script/jquery-1.11.1.js"></script>
<script type="text/javascript" src="/Public/Script/sea/sea.js"></script>
<script type="text/javascript"
		src="/Public/Script/config.js"<?php if(!empty($js_module)): ?> data-module="<?php echo $js_module; ?>"<?php endif; ?>></script>
<script type="text/javascript">seajs.use("/Public/Script/base");</script>
</body>
</html>
