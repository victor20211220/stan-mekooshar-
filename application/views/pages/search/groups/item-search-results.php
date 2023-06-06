<?// dump($group, 1); ?>

	<li data-id="group_<?= $group->id ?>">
		<?= View::factory('parts/groupsava-more', array(
			'group' => $group,
			'avasize' => 'avasize_44',
			'isTooltip' => false,
			'isLinkProfile' => TRUE,
			'isGroupDescripionShort' => TRUE
		)) ?>
		<div class="list_search-result-btn">
			<? if(is_null($group->memberUserId)) : ?>
				<a href="<?= Request::generateUri('groups', 'joinFromSearch', $group->id); ?>"  onclick="return web.ajaxGet(this);" class="icons i-joingroupblue icon-text btn-icon" ><span></span>Join</a>
			<? elseif(!is_null($group->memberUserId) && $group->memberIsApproved == 1) : ?>
				<a href="<?= Request::generateUri('groups', 'joinFromSearch', $group->id); ?>"  onclick="return web.ajaxGet(this);" class="icons i-joingroupblue icon-text btn-icon" ><span></span>Leave</a>
			<? else : ?>
				<a href="<?= Request::generateUri('groups', 'joinFromSearch', $group->id); ?>"  onclick="return web.ajaxGet(this);" class="icons i-joingroupblue icon-text btn-icon" ><span></span>Cancel request</a>
			<? endif; ?>
		</div>
		<div class="list_search-result-data">
<!--			--><?//= date('m.d.Y h:i A', strtotime($sentInvitation->createDate)) ?>
<!--			--><?// if($sentInvitation->typeApproved == 2) : ?>
<!--				<div>-->
<!--					Your invitation was ignored by the user-->
<!--				</div>-->
<!--			--><?// endif; ?>
		</div>
	</li>
