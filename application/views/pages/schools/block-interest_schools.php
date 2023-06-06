<?// dump($interestedSchool, 1); ?>

<div class="block-interest_schools">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>Schools you may be interested in</div>
	</div>

	<? if(isset($interestedSchool) && !empty($interestedSchool['data'])) : ?>
		<ul class="list-items">
			<? foreach($interestedSchool['data'] as $school) : ?>
				<li data-id="school_<?= $school->id ?>">
					<?= View::factory('parts/schoolava-more', array(
						'school' => $school,
						'avasize' => 'avasize_52',
						'isCustomInfo' => TRUE,
						'isLinkProfile' => FALSE,
						'isSchoolNameLink' => TRUE,
						'isFollowButton' => TRUE
					)) ?>
				</li>
			<? endforeach; ?>
		</ul>
	<? else: ?>
		<div class="list-item-empty">
			There are no schools you may be interested in
		</div>
	<? endif; ?>
</div>

