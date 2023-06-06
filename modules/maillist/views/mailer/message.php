Subject: <?=$subject;?>

<?=new View('mailer/email-header')?>
<?=isset($message) ? $message : '' ?>
<?=new View('mailer/email-footer')?>