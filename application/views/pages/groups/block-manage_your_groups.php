<?// dump($myGroups, 1); ?>

<div class="block-manage_your_groups">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>Manage your groups</div>
	</div>

	<ul>
		<? foreach($myGroups['data'] as $group) : ?>
			<li>
				<?= View::factory('parts/groupsava-more', array(
					'group' => $group,
					'isManageButton' => TRUE,
					'isGroupDescripion' => TRUE,
					'isGroupNameLink' => TRUE
				)) ?>
			</li>
		<? endforeach; ?>
	</ul>

</div>

