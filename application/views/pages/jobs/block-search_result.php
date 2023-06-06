<?// dump($jobs, 1); ?>

<div class="block-search_result">
	<div class="title-big">Search Jobs result</div>

		<ul class="list-items is_table">
			<li class="list-items-title">
				<div>Company</div>
				<div>Date post</div>
				<div></div>
			</li>
			<? if(!empty($jobs['data'])) : ?>
				<? foreach($jobs['data'] as $job) : ?>
					<?= View::factory('pages/jobs/item-search_result', array(
						'job' => $job,
					)) ?>
				<? endforeach; ?>
			<? endif; ?>
		</ul>

	<? if(empty($jobs['data'])) : ?>
		<div class="list-item-empty">
			No job search result
		</div>
	<? endif; ?>
</div>
