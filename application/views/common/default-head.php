<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="keywords" content="<?=(isset($keywords) ? $keywords : '')?>" />
<meta name="description" content="<?=(isset($description) ? $description : '')?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<? if ($config->debugEnabled || $oldBrowser): ?>
	<meta name="robots" content="noindex, nofollow" />
<? else: ?>
	<meta name="robots" content="index, follow" />
<? endif ?>

<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
<link rel="icon" type="image/png" href="/favicon-192x192.png" sizes="192x192">
<link rel="icon" type="image/png" href="/favicon-160x160.png" sizes="160x160">
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="msapplication-TileImage" content="/mstile-144x144.png">
<link rel="shortcut icon" href="/resources/images/favicon.ico" type="image/x-icon" />

<title><?=$title?></title>

<link href="/resources/css/normalize.css" rel="stylesheet" type="text/css" media="screen" />
<?
	if (isset($links)) foreach ($links as $i) {
		echo '<link' . Html::attributes($i) . " />\n";
	}
	if (isset($scripts)) foreach ($scripts as $script) {
		echo '<script type="text/javascript" src="' . Url::site($script, 'http') . "\"></script>\n";
	}
?>
