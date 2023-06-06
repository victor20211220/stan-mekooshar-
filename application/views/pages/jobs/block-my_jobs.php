<?// dump($jobs, 1); ?>

<div class="block-my_jobs">
	<div class="title-big">My Jobs</div>

		<ul class="list-items is_table">
			<li class="list-items-title">
				<div>Company</div>
				<div>Job title</div>
				<div>Status</div>
				<div>Applicants</div>
				<div></div>
			</li>
			<? if(!empty($jobs['data'])) : ?>
				<? foreach($jobs['data'] as $job) : ?>
					<?= View::factory('pages/jobs/item-my_jobs', array(
						'job' => $job
					)) ?>
				<? endforeach; ?>
			<? endif; ?>
		</ul>

	<? if(empty($jobs['data'])) : ?>
		<div class="list-item-empty">
			No jobs
		</div>
	<? endif; ?>
</div>
