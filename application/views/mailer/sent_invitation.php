Subject: Welcome to Mekooshar

<?=new View('mailer/email-header')?>
<h1 style="font-size: 24px;"><?= $firstName ?>, welcome to Mekooshar!</h1>
<p><?= $firstName ?> <?= $lastName ?>, sent you this invitation for connect to Mekooshar.</p>
<p>
	You can registered on the page <br/>
	<a href="<?=Url::site()?>"><?=Url::site()?></a>
</p>

<?=new View('mailer/email-footer')?>