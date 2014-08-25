/**
 * Created by zhangwj on 14/8/18.
 */
define(function (require) {
	"use strict";

	var User = require("user");

	User.checkLogin();

	if (User.is_login) {
		if ($("#J_user_nav").length > 0) {
			var htmlCode = new Array();

			htmlCode.push('<li>');
			htmlCode.push('<a href="javascript:;" id="J_user_dropdown">' + User.info.nickname + '</a>');
			htmlCode.push('<ul class="dropdown-item">');
			htmlCode.push('<li><a href="/index.php/account/user">�ҵ���ҳ</a></li>');
			htmlCode.push('<li><a href="/index.php/account/message">�ҵ���Ϣ</a></li>');
			htmlCode.push('<li><a href="/index.php/account/setting">�ʺ�����</a></li>');
			htmlCode.push('<li><a href="/index.php/account/logout">�˳�</a></li>');
			htmlCode.push('</ul>');
			htmlCode.push('</li>');

			$("#J_user_nav").html(htmlCode.join(""));

			$("#J_user_dropdown").parent().mouseenter(function () {
				$(".dropdown-item").show();
			}).mouseleave(function () {
				$(".dropdown-item").hide();
			});

		}
	}

});
