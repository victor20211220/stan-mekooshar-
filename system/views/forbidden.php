<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<title>403 - Forbidden</title>
	<style type="text/css">/*<![CDATA[*/
		body {
			font-family: Trebuchet MS; line-height: 1.5em; margin: 1em;
		}
		h1 {
			display: inline-block; margin: 0; background-color: #c33; color: #fff; padding: 0.4em 0;
		}
		code {
			display: inline-block; white-space: pre; background-color: #eee; margin: 0.5em 0; padding: 0.5em;
		}
		code:before {
			white-space: pre-line;
		}
		.notice {
			color: #aaa;
		}
	/*]]>*/</style>
</head>
<body>
<? if (System::$debugEnabled): ?>
	<h1>Uncaught exception "<?=get_class($exception)?>" <?
		if ($exception instanceof ErrorException) {
			echo '(' . System::$errorLevels[$exception->getSeverity()] . ')';
		}
	?></h1>
	<p>Message: <br />
		<code><?=$exception->getMessage()?></code>
	</p>
	<p>Thrown in: <br />
		<code><?=$exception->getFile() . ':' . $exception->getLine()?></code>
	</p>
	<p>Stack trace: <br />
		<code><?=$exception->getTraceAsString()?></code>
	</p>
	<p class="notice">
		Processed in <?=number_format(microtime(true) - START_TIME, 4)?> seconds,
		using <?=number_format(memory_get_usage(true) / 1048576, 2)?> MB of memory.
	</p>
<? else: ?>
	<h1>Forbidden</h1>
	<p>You don't have permission to access the requested page.</p>
<? endif ?>
</body>
</html>
