<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?=isset($title) ? Html::chars($title) : 'Dashboard' ?></title>
	<link href="/resources/css/style.css" rel="stylesheet" type="text/css" />
	<link href="/resources/css/autoform.css" rel="stylesheet" type="text/css" />
	<link href="/resources/css/admin.css" rel="stylesheet" type="text/css" />
	<?php
		if (isset($links)) foreach ($links as $i) {
			echo '<link' . Html::attributes($i) . ' />'."\n";
		}
	?>
	<script type="text/javascript" src="/resources/js/jquery/jquery.js"></script>
	<?if (isset($scripts)) foreach ($scripts as $script) :?>
		<script type="text/javascript" src="<?=Url::site($script)?>"></script>
	<?endforeach?>
	<script type="text/javascript" src="/resources/js/directory.js"></script>
</head>
	<body class="body-iframe <?=Request::getUserAgent('mobile') == 'iPad' ? 'mobile' : '' ?>">
		<div class="content-iframe">
			<?=isset($content) ? $content : '' ?>
		</div>
	</body>
</html>