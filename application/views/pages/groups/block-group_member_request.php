<?// dump($memberRequests, 1); ?>

<div class="block-group_member_request">
	<? if(count($memberRequests['data']) != 0) : ?>
		<div class="checkbox-control" data-id="1" data-list=".checkbox-control-select" data-select_label="Select all">
			<a href="<?= Request::generateUri('groups', 'acceptRequest', $group->id) . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-accept icon-text btn-icon hidden" ><span></span>Accept</a>
			<a href="<?= Request::generateUri('groups', 'declineRequest', $group->id) . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-deny icon-text btn-icon hidden" ><span></span>Decline</a>
		</div>
		<div class="title-big">Request member</div>
		<ul class="list-items">
			<li class="hidden"></li>
			<? foreach($memberRequests['data'] as $member): ?>
				<?= View::factory('pages/groups/item-group_member', array(
					'member' => $member,
					'group' => $group,
					'isGroupRole' => FALSE
				)) ?>
			<? endforeach ?>
			<li>
				<?= View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE) + $memberRequests['paginator']
				) ?>
			</li>
		</ul>
	<? else : ?>
		<div class="list-item-empty">
			No request
		</div>
	<? endif; ?>
</div>
