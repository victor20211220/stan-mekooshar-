Subject: Order completed

<?=new View('mailer/email-header')?>
<div style="padding-left: 25px; padding-right: 15px;">
	<br>
	<p>Order #<?=$order->token ?> for $<?=$order->amount ?> is paid.</p>
	<p>Login to the system and view details in control panel.</p>
</div>
<br>
<div style="background-color: #f4f4f4; padding-left: 25px; padding-right: 15px; padding-top: 15px; padding-bottom: 15px; min-height: 50px;">
	&nbsp;
</div>
<br>
<?=new View('mailer/email-footer')?>