Subject: Change Password Confirmation

<?=new View('mailer/email-header')?>

<div style="padding-left: 25px; padding-right: 15px;">
	<br>
	<h1 style="font-size: 24px;"><span style="color: #129fcd; text-transform: capitalize;"><b>Dear <?= $firstName ?>,</b></span></h1>
	<div>
		This is a courtesy message to let you know that your mekooshar  password has been successfully changed.<br>
		No response is needed.
	</div>
</div>
<br>

<?=new View('mailer/email-footer')?>