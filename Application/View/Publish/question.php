<?php $this->layout("header"); ?>
<div class="container mb80 clearfix row mt100">
	<div class="col-xs-12">
		<h2 class="common-title">���������</h2>
	</div>
	<form action="" method="post">
		<div class="col-xs-12 col-md-8">
			<div class="edit-post">
				<div class="p">
					<label for="title" class="hidden">����</label>
					<input type="text" name="title" tabindex="1" value="" class="form-control input-lg text-34"
						   autocomplete="off" required spellcheck="false" placeholder="���⣬��һ�仰˵���������">
				</div>
				<div class="p">
					<label for="wmd-input" class="hidden">����</label>
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
				<h3>����ָ��</h3>
				<ul>
					<li>�����뼼����أ�����ȷ�Ĵ𰸣��д��������룬�����ѳ��Թ��Ľ������</li>
					<li>���õ��Ű棬��ȷʹ�� <a href="http://wowubuntu.com/markdown/" target="_blank">Markdown �﷨</a></li>
				</ul>
			</aside>
			<div class="p">
				<input type="submit" name="ask-submit" id="J_ask_submit" class="btn btn-primary btn-lg btn-xl" tabindex="7" style="width: 100%;" value="�ύ����"
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