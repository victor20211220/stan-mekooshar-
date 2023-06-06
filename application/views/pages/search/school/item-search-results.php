<?// dump($school, 1); ?>

	<li data-id="school_<?= $school->id ?>">
		<?= View::factory('parts/schoolava-more', array(
			'school' => $school,
			'avasize' => 'avasize_44',
			'isTooltip' => false,
			'isLinkProfile' => TRUE,
			'isSchoolDescripion' => TRUE
		)) ?>
		<div class="list_search-result-btn">
			<? if(is_null($school->followUserId)) : ?>
				<a href="<?= Request::generateUri('schools', 'followFromSearch', $school->id); ?>"  onclick="return web.ajaxGet(this);" class="icons i-accept icon-text btn-icon" ><span></span>Follow</a>
			<? else: ?>
				<a href="<?= Request::generateUri('schools', 'followFromSearch', $school->id); ?>"  onclick="return web.ajaxGet(this);" class="icons i-close icon-text btn-icon" ><span></span>Unfollow</a>
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
