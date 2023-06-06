<?// dump($groups_interested, 1); ?>

<div class="block-interest_groups">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>Interest groups</div>
	</div>
	<ul class="list-items">
		<? foreach($groups_interested['data'] as $group) : ?>
			<li data-id="group_<?= $group->id ?>">
				<?= View::factory('parts/groupsava-more', array(
					'group' => $group,
					'avasize' => 'avasize_52',
					'isGroupNameLink' => TRUE,
					'isGroupIndustry' => TRUE,
					'isFollowButton' => TRUE
				)) ?>
			</li>
		<? endforeach ?>
	</ul>

</div>

