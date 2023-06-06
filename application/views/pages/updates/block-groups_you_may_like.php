<?// dump($groupsYouMayLike, 1); ?>

<div class="block-groups_you_may_like">
	<div class="content-title">
		<div>Groups you may like</div>
	</div>
	<ul class="list-items">
		<? foreach($groupsYouMayLike['data'] as $group) : ?>
			<li data-id="group_<?= $group->id ?>">
				<?= View::factory('parts/groupsava-more', array(
					'group' => $group,
					'avasize' => 'avasize_52',
					'isLinkProfile' => TRUE,
					'isGroupDescripion' => TRUE,
					'maxSizeDescription' => 130
				)) ?>
			</li>
		<? endforeach ?>
	</ul>

</div>

