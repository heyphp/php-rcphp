<?php $this->layout("header"); ?>
<div class="container mb80 clearfix row mt100">
	<div class="col-xs-12">
		<h2 class="common-title">提出新问题</h2>
	</div>
	<form action="" method="post">
		<div class="col-xs-12 col-md-8">
			<div class="edit-post">
				<div class="p">
					<label for="title" class="hidden">标题</label>
					<input type="text" name="title" tabindex="1" value="" class="form-control input-lg text-34"
						   autocomplete="off" required spellcheck="false" placeholder="标题，用一句话说清你的问题">
				</div>
				<div class="p">
					<label for="wmd-input" class="hidden">内容</label>
					<textarea id="wmd-input" class="form-control mono mousetrap textarea-14" name="text"
							  tabindex="2" required autocomplete="off" rows="15"></textarea>
				</div>
				<div id="wmd-preview" class="fmt preview"></div>
			</div>
			<!-- end #content -->
		</div>
		<!-- end #edit-main -->
		<div class="col-xs-12 col-md-4">
			<aside class="warn edit-guide">
				<h3>提问指南</h3>
				<ul>
					<li>内容与技术相关，有明确的答案，有代码贴代码，附上已尝试过的解决方案</li>
					<li>良好的排版，正确使用 <a href="http://wowubuntu.com/markdown/" target="_blank">Markdown 语法</a></li>
				</ul>
			</aside>
			<div class="p">
				<input type="submit" name="ask-submit" id="J_ask_submit" class="btn btn-primary btn-lg btn-xl" tabindex="7" value="提交问题"
					   disabled="disabled">
			</div>
		</div>
		<!-- end #edit-secondary -->
	</form>
</div>
</div>
<?php $this->layout("footer"); ?>
<script type="text/javascript" src="/Public/Script/Markdown.Converter.js"></script>
<script type="text/javascript" src="/Public/Script/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="/Public/Script/Markdown.Editor.js"></script>