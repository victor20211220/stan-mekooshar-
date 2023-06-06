<?// dump($memberUser, 1); ?>

<div class="block-group_member_user">
	<? if(count($memberUser['data']) != 0) : ?>
		<div class="checkbox-control" data-id="1" data-list=".checkbox-control-select" data-select_label="Select all">
			<a href="<?= Request::generateUri('groups', 'changeRoleToAdmin', $group->id) . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon hidden" ><span></span>Change role to admin</a>
			<a href="<?= Request::generateUri('groups', 'removeMember', $group->id) . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon hidden" ><span></span>Delete memeber</a>
		</div>
		<div class="title-big">Members</div>
		<ul class="list-items">
			<li class="hidden"></li>
			<? foreach($memberUser['data'] as $member): ?>
				<?= View::factory('pages/groups/item-group_member', array(
					'member' => $member,
					'group' => $group,
					'isGroupRole' => TRUE
				)) ?>
			<? endforeach ?>
			<li>
				<?= View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE) + $memberUser['paginator']
				) ?>
			</li>
		</ul>
	<? else : ?>
		<div class="list-item-empty">
			No members
		</div>
	<? endif; ?>
</div>
