/**
 * Created by zhangwj on 14/8/28.
 */
define(function (require) {
	"use strict";

	$(function () {
		var converter1 = Markdown.getSanitizingConverter();
		var editor1 = new Markdown.Editor(converter1);
		editor1.run();

		$("#wmd-input").on("input", function () {
			if ($(this).val().trim() != '') {
				$("#J_ask_submit").attr("disabled", false);
			} else {
				$("#J_ask_submit").attr("disabled", true);
			}
		})
	});
});
