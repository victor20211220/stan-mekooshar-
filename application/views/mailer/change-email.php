Subject: Email change

<?=new View('mailer/email-header')?>
<p>Your email was changed to <b><?=$email ?></b>. From now all your messages can be viewed at <?=$email ?></p> 
<?=new View('mailer/email-footer')?>