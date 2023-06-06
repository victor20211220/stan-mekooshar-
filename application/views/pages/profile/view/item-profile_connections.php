<?// dump($connection, 1);
?><li>
	<?=  View::factory('parts/userava-more', array(
		'isCustomInfo' => TRUE,
		'isTooltip' => FALSE,
		'avasize' => 'avasize_52',
		'ouser' => $connection,
		'isLinkProfile' => FALSE,
		'isUsernameLink' => TRUE,
		'isBtnAddConnection' => TRUE
	)); ?>
</li>