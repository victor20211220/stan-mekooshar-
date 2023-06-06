<?// dump($job, 1); ?>
<?// dump($applicants, 1); ?>

<div class="block-applicants">
	<div class="title-big">My Jobs Applicants</div>

		<ul class="list-items is_table">
			<li class="list-items-title">
				<div>Applicants</div>
				<div>View application</div>
				<div></div>
			</li>
			<? if(!empty($applicants['data'])) : ?>
				<? foreach($applicants['data'] as $applicant) : ?>
					<?= View::factory('pages/jobs/item-applicants', array(
						'applicant' => $applicant,
						'job' => $job
					)) ?>
				<? endforeach; ?>
			<? endif; ?>
		</ul>

	<? if(empty($applicants['data'])) : ?>
		<div class="list-item-empty">
			No job applicants
		</div>
	<? endif; ?>
</div>
