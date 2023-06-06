!~~
Uncaught exception "<?=get_class($exception)?>" <?
		if ($exception instanceof ErrorException) {
			echo '(' . System::$errorLevels[$exception->getSeverity()] . ')';
		}
	?>


Message: <?=$exception->getMessage()?>


Thrown in: <?=$exception->getFile() . ':' . $exception->getLine()?>


Stack trace:
<?=$exception->getTraceAsString()?>

~~.
