Subject: Confirm email
MIME-Version: 1.0
Content-Type: text/html; charset=utf-8

<html>
<body>
<div style="padding: 20px 35px">
	<h3>Next user has not entered email in his account:</h3>
	<div><b>Login:</b> <?=$login ?></div>
	<div><b>Email:</b> <?=$email; ?></div>
	<p>Please enter email in this <a target="_blank" href="<?=Request::$protocol ?>://<?=$_SERVER['HTTP_HOST'] ?>/dashboard/users/edit/<?=$id ?>/" >account</a>, change password and notify him.</p>
</div>
</body>
</html>