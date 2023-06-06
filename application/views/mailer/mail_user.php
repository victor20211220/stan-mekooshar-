Subject: Your new account

<?=new View('mailer/email-header')?>
<h3>Hi, <?=$firstName ?>!</h3>
<p>Your new account:</p>
<p><b>Login:</b> <?=$name ?></p>
<p><b>Password:</b> <?=$password ?></p>
<p>Thank you!</p>
<?=new View('mailer/email-footer')?>