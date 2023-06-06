Subject: Resetting the password

<?=new View('mailer/email-header')?>

<div style="padding-left: 25px; padding-right: 15px;">
	<br>
	<h1 style="font-size: 24px;"><span style="color: #129fcd; text-transform: capitalize;"><b>Hi <?= $firstName ?>,</b></span></h1>
	<div>
		Changing your password is simple. Please use the link below within 24 hours.
	</div>
</div>
<br><br><br>
<div style="background-color: #f4f4f4; padding-left: 25px; padding-right: 15px; padding-top: 15px; padding-bottom: 15px;">
	<a href="<?=Url::site(Request::$controller . 'confirm/?code=' . $code)?>"><?=Url::site('/confirm/?code=' . $code)?></a>
</div>
<br>

<?=new View('mailer/email-footer')?>