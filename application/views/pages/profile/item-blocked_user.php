<?// dump($user, 1); ?>

<li data-id="profile_<?= $user->id ?>">
	<?= View::factory('parts/userava-more', array(
		'ouser' => $user,
		'isCustomInfo' => FALSE,
		'isShowName' => TRUE,
		'avasize' => 'avasize_44',
		'isTooltip' => false,
		'isLinkProfile' => FALSE,
		'isUsernameLink' => TRUE,
		'isBtnDeleteBlockUser' => TRUE
	)) ?>
</li>