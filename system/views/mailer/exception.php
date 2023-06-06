Subject: <?=t('title')?> - Exception report

<?=new View('mailer/email-header', array('title' => 'Exception report')) ?>
<? $url = Request::$protocol . '://' . Request::$host . Request::$uri . Request::$query; ?>
<h4 style="font-size: 16px;"><a href="<?=$url ?>"><?=$url ?></a></h4>
<p>
	Time: <?=date('Y-m-d H:i:s', time())?>
</p>
<? if(is_string($exception)) : ?>
	<p style="margin: 10px 0;">Message: <br />
		<?=$exception ?>
	</p>
<? else : ?>
	<p style="margin: 10px 0;">Message: <br />
		<code style="white-space: pre-line; background-color: #EEEEEE; display: inline-block; margin: 0.5em 0; padding: 0.5em; white-space: pre-wrap;"><?=$exception->getMessage()?></code>
	</p>
	<p style="margin: 10px 0;">Thrown in: <br />
		<code style="white-space: pre-line; background-color: #EEEEEE; display: inline-block; margin: 0.5em 0; padding: 0.5em; white-space: pre-wrap;"><?=$exception->getFile() . ':' . $exception->getLine()?></code>
	</p>
	<p style="margin: 10px 0;">Stack trace: <br />
		<code style="white-space: pre-line; background-color: #EEEEEE; display: inline-block; margin: 0.5em 0; padding: 0.5em; white-space: pre-wrap;"><?=$exception->getTraceAsString()?></code>
	</p>
<? endif; ?>
<p>
	<code style="white-space: pre-line; background-color: #EEEEEE; display: inline-block; margin: 0.5em 0; padding: 0.5em; white-space: pre-wrap;">
		<? var_dump($_SERVER) ?>
	</code>
</p>


<?=new View('mailer/email-footer')?>