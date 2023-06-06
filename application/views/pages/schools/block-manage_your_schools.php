<?// dump($schoolsManage, 1); ?>

<div class="block-manage_your_schools">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>Manage your schools</div>
	</div>

	<ul class="list-items">
		<? foreach($schoolsManage['data'] as $school) : ?>
			<li>
				<?= View::factory('parts/schoolava-more', array(
					'school' => $school,
					'isManageButton' => TRUE,
					'isSchoolNameLink' => TRUE
				)) ?>
			</li>
		<? endforeach; ?>
	</ul>
</div>

