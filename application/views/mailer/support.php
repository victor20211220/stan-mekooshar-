Subject: New message for support

<?=new View('mailer/email-header')?>
<div style="padding-left: 25px; padding-right: 15px;">
	<br>

	<div>
		You have new message for support from Mekooshar.
	</div>
	<div style="padding-top: 10px;">
		<b>Name:</b> <?= $name ?><br />
		<b>Company name:</b> <?= $company_name ?><br />
		<b>E-mail address:</b> <?= $email ?>
	</div>
</div>
<br><br><br>
<div style="background-color: #f4f4f4; padding-left: 25px; padding-right: 15px; padding-top: 15px; padding-bottom: 15px;">
	<?= $message ?>
</div>
<br>
<?=new View('mailer/email-footer')?>