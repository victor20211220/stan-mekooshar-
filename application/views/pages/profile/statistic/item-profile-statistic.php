<?// dump($connectionWhoVisit, 1); ?>

<li>
	<div>
		<?= View::factory('parts/userava-more', array(
			'isCustomInfo' => TRUE,
			'ouser' => $connectionWhoVisit,
			'avasize' => 'avasize_44',
			'isTooltip' => false,
		)) ?>

		<div class="list-panel-bottom">
			<b><?= date('m.d.Y', strtotime($connectionWhoVisit->createDate)) ?></b>
			<?= date('h:i A', strtotime($connectionWhoVisit->createDate)) ?>
		</div>
	</div>
</li>