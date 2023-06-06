Subject: Confirmation email

<?=new View('mailer/email-header')?>
<div style="padding-left: 25px; padding-right: 15px;">
	<br>
	<h1 style="font-size: 24px;"><span style="color: #129fcd; text-transform: capitalize;"><b><?= $firstName ?></b></span>, welcome to Mekooshar!</h1>
	<div>
		Connect with your colleagues, classmates, friends, and find job and professional opportunities.<br>
		Keep up with other jewish professionals in all over the world.<br>
		Start now.
	</div>
	<div style="padding-top: 10px;">
		<b>Your login:</b> <?= $email ?><br />
		<b>Your password:</b> <?= $cryptpassw ?>
	</div>
</div>
<br><br><br>
<div style="background-color: #f4f4f4; padding-left: 25px; padding-right: 15px; padding-top: 15px; padding-bottom: 15px;">
	Please folow the link below to confirm your registration: <br/>
	<a href="<?=Url::site('/confirm/?code=' . $code)?>"><?=Url::site('/confirm/?code=' . $code)?></a>
</div>
<br>
<?=new View('mailer/email-footer')?>