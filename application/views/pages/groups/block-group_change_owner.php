<?// dump(memberAdmin, 1); ?>

<div class="block-group_member_admin">
	<? if(count($memberAdmin['data']) != 0) : ?>
		<div class="checkbox-control" data-id="1" data-list=".checkbox-control-select" data-select_type="one" data-select_label="Select all">
			<a href="<?= Request::generateUri('groups', 'changeRoleToOwner', $group->id) . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon hidden" ><span></span>Change role to owner</a>
		</div>
		<div class="title-big">Change owner</div>
		<ul class="list-items">
			<li class="hidden"></li>
			<? foreach($memberAdmin['data'] as $member): ?>
				<?= View::factory('pages/groups/item-group_member', array(
					'member' => $member,
					'group' => $group,
					'isGroupRole' => TRUE
				)) ?>
			<? endforeach ?>
			<li>
				<?= View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE) + $memberAdmin['paginator']
				) ?>
			</li>
		</ul>
	<? else : ?>
		<div class="list-item-empty">
			No administrators
		</div>
	<? endif; ?>
</div>
