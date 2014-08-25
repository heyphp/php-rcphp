/**
 * Created by zhangwj on 14/8/18.
 */
define(function (require, exports, module) {
	"use strict";

	var user = {};

	user.is_login = false;

	user.info = null;

	/**
	 * ¼ì²âÓÃ»§µÇÂ¼
	 */
	user.checkLogin = function () {
		$.ajax({
			type: 'get',
			url: '/index.php/common/checkLogin',
			async: false,
			success: function (data) {
				var json_data = eval('(' + data + ')');

				if (json_data.code == 200) {
					user.info = json_data.data;

					user.is_login = true;
				}

				return user.is_login;
			}
		});
	};

	module.exports = user;
});