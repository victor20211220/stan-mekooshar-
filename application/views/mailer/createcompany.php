Subject: Welcome to Mekooshar

<?=new View('mailer/email-header')?>

<div style="padding-left: 25px; padding-right: 15px;">
	<br>
	<h1 style="font-size: 24px;"><span style="color: #129fcd; text-transform: capitalize;"><b>Hi <?= $firstName ?>,</b></span></h1>
	<div>
		You have successfully created the company page "<?= $companyName ?>" at Mekooshar.
	</div>
</div>
<br><br><br>
<div style="background-color: #f4f4f4; padding-left: 25px; padding-right: 15px; padding-top: 15px; padding-bottom: 15px;">
	Please follow the link below to confirm your corporate mail:<br/>
	<a href="<?=Url::site('/confirm/?code=' . $code)?>"><?=Url::site('/confirm/?code=' . $code)?></a>
</div>
<br>

<?=new View('mailer/email-footer')?>