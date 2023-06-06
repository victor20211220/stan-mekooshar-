<?// dump($results, 1); ?>

<div class="search-list_job">
	<div class="list_job-title">
		<div class="title-small">Search in</div>
		<a class="btn-roundblue <?= (Request::$action == 'index') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'index') ?>">All</a>
		<a class="btn-roundblue <?= (Request::$action == 'people') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'people') ?>">People</a>
		<a class="btn-roundblue <?= (Request::$action == 'company') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'company') ?>">Companies</a>
		<a class="btn-roundblue <?= (Request::$action == 'group') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'group') ?>">Groups</a>
		<a class="btn-roundblue <?= (Request::$action == 'school') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'school') ?>">Schools</a>
		<a class="btn-roundblue <?= (Request::$action == 'job') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'job') ?>">Job</a>
	</div>
	<div class="title-big">Found <span><?= $results['paginator']['count'] ?></span> results</div>

	<div class="block-search_result">
		<ul class="list-items is_table">
			<? if(!empty($results['data'])) : ?>
				<li class="list-items-title">
					<div>Company</div>
					<div>Date post</div>
					<div></div>
				</li>
				<? foreach($results['data'] as $job) : ?>
					<?= View::factory('pages/jobs/item-search_result', array(
						'job' => $job,
					)) ?>
				<? endforeach; ?>
				<li>
					<?= View::factory('common/default-pages', array(
							'isBand' => TRUE,
							'autoScroll' => TRUE
						) + $results['paginator']) ?>
				</li>
			<? endif; ?>
		</ul>
	</div>
</div>