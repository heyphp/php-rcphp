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
			htmlCode.push('<li><a href="/index.php/account/user">我的主页</a></li>');
			htmlCode.push('<li><a href="/index.php/account/message">我的消息</a></li>');
			htmlCode.push('<li><a href="/index.php/account/settings">帐号设置</a></li>');
			htmlCode.push('<li><a href="/index.php/account/logout">退出</a></li>');
			htmlCode.push('</ul>');
			htmlCode.push('</li>');

			$("#J_user_nav").html(htmlCode.join(""));

			$("#J_user_dropdown").click(function () {
				if ($(".dropdown-item").css("display") == "none") {
					$(this).parent().addClass("current");
					$(".dropdown-item").show();
				}
				else {
					$(this).parent().removeClass("current");
					$(".dropdown-item").hide();
				}
			});

		}
	}

});
