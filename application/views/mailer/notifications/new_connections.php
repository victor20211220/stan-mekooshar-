Subject: Please add me to you network

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
		I'd like to connect with you on Mekooshar.<br/><br/>

		<?= (string) View::factory('mailer/notifications/profile_ava', array(
			'user' => $user,
			'ouser' => $ouser
		)) ?>

		<br/><br/>

		<a class="blue-btn" style="background-color: #129fcd; color: #FFF; font-size: 12px; height: 24px; line-height: 23px; display: inline-block; width: auto; margin-top: 5px; padding: 0 10px; overflow: hidden; text-decoration: none;" href="<?= Request::generateUri('connections', 'acceptReceived', $connection_id) ?>">
			Apply
		</a>
		<a class="blue-btn" style="background-color: #129fcd; color: #FFF; font-size: 12px; height: 24px; line-height: 23px; display: inline-block; width: auto; margin-top: 5px; padding: 0 10px; overflow: hidden; text-decoration: none;" href="<?= $profileLink ?>">
			View Profile
		</a>

	</div>
</div>
<br><br><br>


<?=new View('mailer/email-footer')?>