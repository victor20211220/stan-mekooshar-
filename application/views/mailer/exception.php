Subject: Exception report <?=Config::getInstance()->debugEnabled ? '[TEST]' : '' ?>

<?=new View('mailer/email-header', array('title' => 'Exception report')) ?>
<? $url = Url::site(Url::current()); ?>
<h4 style="font-size: 16px;"><a href="<?=$url ?>"><?=$url ?></a></h4>
<div>
	Time: <?=date('Y-m-d H:i:s', time())?>
</div>
<? if(is_string($exception)) : ?>
	<div style="margin: 10px 0;">Message: <br />
		<?=$exception ?>
	</div>
<? else : ?>
	<div style="margin: 10px 0;">Message: <br />
		<code style="white-space: pre-line; background-color: #EEEEEE; display: inline-block; margin: 0.5em 0; padding: 0.5em; white-space: pre-wrap;"><?=$exception->getMessage()?></code>
	</div>
	<div style="margin: 10px 0;">Thrown in: <br />
		<code style="white-space: pre-line; background-color: #EEEEEE; display: inline-block; margin: 0.5em 0; padding: 0.5em; white-space: pre-wrap;"><?=$exception->getFile() . ':' . $exception->getLine()?></code>
	</div>
	<div style="margin: 10px 0;">Stack trace: <br />
		<code style="white-space: pre-line; background-color: #EEEEEE; display: inline-block; margin: 0.5em 0; padding: 0.5em; white-space: pre-wrap;"><?=$exception->getTraceAsString()?></code>
	</div>
<? endif; ?>
<div>
	<code style="white-space: pre-line; background-color: #EEEEEE; display: inline-block; margin: 0.5em 0; padding: 0.5em; white-space: pre-wrap;">
		<? var_dump($_SERVER) ?>
	</code>
</div>

<?=new View('mailer/email-footer')?>