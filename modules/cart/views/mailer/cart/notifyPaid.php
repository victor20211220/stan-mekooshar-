Subject: Order completed

<?=new View('mailer/email-header')?>
<div style="padding-left: 25px; padding-right: 15px;">
	<br>
	<p>Order #<?=$order->token ?> for $<?=$order->amount ?> is paid.</p>
</div>
<br><br><br>
<?=new View('mailer/email-footer')?>