<?// dump($tags, 1); ?>
<?// dump($companies, 1); ?>
<?// dump($regions, 1); ?>
<?// dump($right, 1); ?>

<div class="connections">
	<?= View::factory('parts/parts-right_big', array(
		'left' => $left,
		'right' => $right
	)); ?>
</div>