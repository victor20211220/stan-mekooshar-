<?// dump($connection, 1); ?><li data-id="connection_<?= $connection->id ?>">
				<div class="checkbox-control-select" data-id="<?= $connection->id ?>"></div>
				<?= View::factory('parts/userava-more', array(
					'isTooltip' => FALSE,
					'isCustomInfo' => TRUE,
					'avasize' => 'avasize_52',
					'ouser' => $connection
				)) ?>
			</li>
