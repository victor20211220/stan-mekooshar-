<?// dump($memberAdmin, 1); ?>
<?// dump($group, 1); ?>

<div class="block-group_member_admin">
	<? if(count($memberAdmin['data']) > 1) : ?>
		<div class="checkbox-control" data-id="1" data-list=".checkbox-control-select" data-select_label="Select all">
			<a href="<?= Request::generateUri('groups', 'changeRoleToMember', $group->id) . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon hidden" ><span></span>Change role to member</a>
		</div>
	<? endif ?>
	<div class="title-big">Administrators</div>
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
</div>
