/**
 * Created by zhangwj on 14/8/18.
 */
"use strict";

seajs.config({
	'alias': {
		'jquery.cookie': '/Public/Script/jquery.cookie',
		'user': '/Public/Script/user'
	},
	'map': [
		[ /^(.*\.(?:css|js))(.*)$/i, '$1?t=20140818']
	]
});
(function () {

	if (!window.console) {
		window.console = {};

		window.console.log = function () {
		};
		window.console.error = function () {
		};
	}

	var scripts = document.scripts,
		script = scripts[scripts.length - 1],
		boot = script.getAttribute("data-module"),
		dir = script.getAttribute("src");

	dir = dir.slice(0, dir.lastIndexOf('/') + 1);

	window.addEventListener("load", function () {
		if (boot) {
			seajs.use(dir + boot);
		}
	}, false);
})();