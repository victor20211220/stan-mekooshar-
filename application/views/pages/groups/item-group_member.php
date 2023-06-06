<?// dump($member, 1); ?>
<?// dump($isGroupRole, 1); ?>
<?// dump($group, 1); ?>

<li data-id="member_<?= $member->userId ?>">
	<div>
		<?= View::factory('parts/userava-more', array(
			'ouser' => $member,
			'avasize' => 'avasize_94',
			'isTooltip' => FALSE,
			'isCustomInfo' => TRUE,
			'isUsernameLink' => TRUE,
			'isGroupRole' => $isGroupRole,
			'groupOwnerId' => $group->user_id

		)) ?>
	</div><div class="<?= ($member->userId != $group->user_id) ? 'checkbox-control-select' : null ?>" data-id="<?= $member->id ?>"></div>
</li>