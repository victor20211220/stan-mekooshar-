<?// dump($results, 1); ?>
<?// dump($f_findshort, 1); ?>
<?// dump($query, 1); ?>
<?
	$isEmptyFilter = TRUE;
	if($query['region'] || $query['company'] || $query['industrypeople'] || $query['school']) {
		$isEmptyFilter = FALSE;
	}
	$value = trim($query['firstName']);
	if(!empty($value)) {
		$isEmptyFilter = FALSE;
	}
	$value = trim($query['lastName']);
	if(!empty($value)) {
		$isEmptyFilter = FALSE;
	}

?>

<div class="search-list_people page_public_search">
	<div class="list_people-title">
		<?= $f_findshort->form->render(); ?>
	</div>

	<? if($results['paginator']['count'] != 0 && !$isEmptyFilter) : ?>
		<div class="title-big">Found <span><?= $results['paginator']['count'] ?></span> results</div>
		<ul class="list-items">
			<? foreach($results['data'] as $result) : ?>
				<?= View::factory('pages/search_people_as_public/item-search-results', array(
					'result' => $result
				)) ?>
			<? endforeach; ?>
		</ul>

	<? else: ?>
		<div class="list-item-empty">
			Nothing found
		</div>
	<? endif; ?>
</div>