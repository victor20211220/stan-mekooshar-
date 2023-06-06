Subject: Create school request

<?=new View('mailer/email-header')?>
<h1 style="font-size: 24px;">Hello, <?= $firstName ?>!</h1>
<p>You have created new school "<?= $schoolName ?>" on Mekooshar.com.</p>
<p>
	Follow the link below to confirm your email. If you haven't sent the request, please ignore this message: <br/>
	<a href="<?=Url::site('/confirm/?code=' . $code)?>"><?=Url::site('/confirm/?code=' . $code)?></a>
</p>

<?=new View('mailer/email-footer')?>