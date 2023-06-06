<?// dump($f_Groups_FindMemberInGroup, 1); ?>
<?// dump($groupMembers, 1); ?>

<div class="block-members">
	<div class="bg-blue bg-brown">
		<div class="text-bgtitle"><span><?= count($groupMembers['data']) ?></span> RESULTS</div>
		<div>
			<? $f_Groups_FindMemberInGroup->form->header(); ?>
			<? $f_Groups_FindMemberInGroup->form->render('fields'); ?>
			<? $f_Groups_FindMemberInGroup->form->render('submit'); ?>
			<? $f_Groups_FindMemberInGroup->form->footer(); ?>
		</div>
	</div>

	<ul class="block-group_list_member">
		<? foreach($groupMembers['data'] as $member) : ?>
			<li>
				<? echo View::factory('parts/userava-more', array(
					'isCustomInfo' => TRUE,
					'isTooltip' => FALSE,
					'avasize' => 'avasize_52',
					'ouser' => $member
				)); ?>
			</li>
		<? endforeach; ?>
	</ul>


</div>
