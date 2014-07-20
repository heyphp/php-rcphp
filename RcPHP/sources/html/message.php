<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gb2312"/>
	<title>��ת��ʾ</title>
	<style type="text/css">
		* {
			padding: 0;
			margin: 0;
		}

		body {
			background: #fff;
			font-family: '΢���ź�';
			color: #333;
			font-size: 16px;
		}

		.system-message {
			padding: 24px 48px;
		}

		.system-message h1 {
			font-size: 100px;
			font-weight: normal;
			line-height: 120px;
			margin-bottom: 12px;
		}

		.system-message .jump {
			padding-top: 10px
		}

		.system-message .jump a {
			color: #333;
		}

		.system-message .success {
			line-height: 1.8em;
			font-size: 36px
		}

		.system-message .detail {
			font-size: 12px;
			line-height: 20px;
			margin-top: 12px;
			display: none
		}

		.copyright {
			padding: 12px 48px;
			color: #999;
		}

		.copyright a {
			color: #000;
			text-decoration: none;
		}
	</style>
</head>
<body>
<div class="system-message">
	<p class="success"><?php echo $message; ?></p>

	<p class="detail"></p>

	<p class="jump">
		ҳ���Զ� <a id="href" href="<?php echo $gotoUrl; ?>">��ת</a> �ȴ�ʱ�䣺 <b id="wait"><?php echo $limitTime; ?></b>
	</p>
</div>
<div class="copyright">
	<p>RcPHP<sup><?php echo RCPHP_VERSION ?></sup> { RcPHP Framework }</p>
</div>
<script type="text/javascript">
	(function () {
		var wait = document.getElementById('wait'), href = document.getElementById('href').href;
		var interval = setInterval(function () {
			var time = --wait.innerHTML;
			if (time <= 0) {
				location.href = href;
				clearInterval(interval);
			}
			;
		}, 1000);
	})();
</script>
</body>
</html>
