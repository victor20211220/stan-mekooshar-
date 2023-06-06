Subject: Accepted your invitation

<?=new View('mailer/email-header')?>
<?

if(isset($ouser->userAlias) && !empty($ouser->userAlias)) {
	$profileLink = Request::generateUri($ouser->userAlias, 'index');
} else {
	$profileLink = Request::generateUri('profile', $ouser->id);
}

?>


<div style="padding-left: 25px; padding-right: 15px;">
	<br>
	<h1 style="font-size: 24px;"><span style="color: #129fcd; text-transform: capitalize;"><b>Hi <?= $firstName ?>,</b></span></h1>
	<div>
		User has accepted your invitation.<br/><br/>

		<?= (string) View::factory('mailer/notifications/profile_ava', array(
			'user' => $user,
			'ouser' => $ouser
		)) ?>

		<br/><br/>

		<a class="blue-btn" style="background-color: #129fcd; color: #FFF; font-size: 12px; height: 24px; line-height: 23px; display: inline-block; width: auto; margin-top: 5px; padding: 0 10px; overflow: hidden; text-decoration: none;" href="<?= $profileLink ?>">
			View Profile
		</a>
		<a class="blue-btn" style="background-color: #129fcd; color: #FFF; font-size: 12px; height: 24px; line-height: 23px; display: inline-block; width: auto; margin-top: 5px; padding: 0 10px; overflow: hidden; text-decoration: none;" href="<?= Request::generateUri('messages', 'sentMessageFromProfile', $ouser->id) ?>">
			Send a message
		</a>

	</div>
</div>
<br><br><br>


<?=new View('mailer/email-footer')?>