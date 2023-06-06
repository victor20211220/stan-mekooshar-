<? // dump($results, 1); ?>

<div class="search-list_all ">
	<div class="list_people-title">
		<div class="title-small">Search in</div>
		<a class="btn-roundblue <?= (Request::$action == 'index') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'index') ?>">All</a>
		<a class="btn-roundblue <?= (Request::$action == 'people') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'people') ?>">People</a>
		<a class="btn-roundblue <?= (Request::$action == 'company') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'company') ?>">Companies</a>
		<a class="btn-roundblue <?= (Request::$action == 'group') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'group') ?>">Groups</a>
		<a class="btn-roundblue <?= (Request::$action == 'school') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'school') ?>">Schools</a>
		<a class="btn-roundblue <?= (Request::$action == 'job') ? 'active' : null ?>" href="<?= Request::generateUri('search', 'job') ?>">Job</a>
	</div>
	<div class="title-big">Found <span><?= $results['paginator']['count'] ?></span> results</div><? // TODO ?>
	<ul class="list-items">
		<li class="hidden"></li>
		<? foreach ($results['data'] as $result) : ?>
			<?= View::factory('pages/search/all/item-search-results', array(
				'result' => $result
			)) ?>
		<? endforeach; ?>
		<li>
			<?= View::factory('common/default-pages', array(
					'isBand'     => TRUE,
					'autoScroll' => TRUE
				) + $results['paginator']) ?>
		</li>
	</ul>
</div>