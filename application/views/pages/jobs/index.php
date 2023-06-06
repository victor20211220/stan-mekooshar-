<?// dump($f_Jobs_SearchJob, 1); ?>

<div class="jobs">
<!--	--><?//= View::factory('parts/parts-left_big', array(
//		'lefttop' => '',
//		'leftmiddle' => '',
//		'left' => View::factory('pages/jobs/block-search_filters', array(
//			'f_Jobs_SearchJob' => $f_Jobs_SearchJob
//		)),
//		'right' => View::factory('pages/jobs/rightpanel', array(
//			'isManageJobsApplication' => TRUE
//		))
//	)) ?>
	<?= View::factory('pages/jobs/block-search_filters', array(
		'f_Jobs_SearchJob' => $f_Jobs_SearchJob
	)); ?>
</div>