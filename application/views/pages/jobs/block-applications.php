<?// dump($jobs, 1); ?>

<div class="block-applications">
	<div class="title-big">My Jobs Applications</div>

		<ul class="list-items is_table">
			<li class="list-items-title">
				<div>Companies</div>
				<div>Job status</div>
				<div></div>
			</li>
			<? if(!empty($jobs['data'])) : ?>
				<? foreach($jobs['data'] as $job) : ?>
					<?= View::factory('pages/jobs/item-applications', array(
						'job' => $job
					)) ?>
				<? endforeach; ?>
			<? endif; ?>
		</ul>

	<? if(empty($jobs['data'])) : ?>
		<div class="list-item-empty">
			No job applications
		</div>
	<? endif; ?>
</div>
