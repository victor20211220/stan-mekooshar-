<?// dump($job, 1); ?>

<div class="block-job_view">
	<div class="title-big">Job - <?= $job->title ?></div>
	<div class="line"></div>
	<div class="job_view-company_short_info">
		<?= View::factory('pages/jobs/block-job_buttons', array(
			'job' => $job,
			'from' => 'job',
			'isShowStatus' => TRUE
		)) ?>
		<?
//			dump($job, 1);
			$industry = t('industries.' . $job->industry);

			$location = array();
			$location[] = t('countries.' . $job->country);
			if($job->country == 'US') {
				$location[] = t('states.' . $job->state);
			} else {
				$location[] = $job->state;
			}
			$location[] = $job->city;
		?>
		<?= View::factory('parts/companiesava-more', array(
			'company' => $job,
			'avasize' => 'avasize_52',
			'isCompanyNameLink' => TRUE,
			'keyId' => 'companyId',
			'otherInfo' => '<div>' . $industry . '</div><div>' . implode(', ', $location) . '</div>'
		))?>
		<div><b>Employment: </b><?= t('employment.' . $job->employment) ?></div>
		<? if(!empty($job->skillsName)) : ?>
			<div class="title-big">Required skills</div>
			<div><?= $job->skillsName ?></div>
		<? endif; ?>
	</div>
	<div class="job_view-description">
		<div class="title-big">Description</div>
		<div><?= $job->description ?></div>
	</div>
	<div class="job_view-about_company">
		<div class="title-big">About the company</div>
		<div><?= $job->about ?></div>
	</div>
</div>
