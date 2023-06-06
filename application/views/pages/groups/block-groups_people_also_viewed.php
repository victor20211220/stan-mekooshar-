<?// dump($peopleAlsoViewed, 1); ?>

<div class="block-groups_people_also_viewed">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>People also viewed</div>
	</div>
	<ul class="list-items">
		<? foreach($peopleAlsoViewed['data'] as $group) : ?>
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

