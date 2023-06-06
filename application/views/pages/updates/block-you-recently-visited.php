<?// dump($myVisits, 1); ?>

<? if(!empty($myVisits['data'])) : ?>
	<div class="block-you-recently-visited">
		<div class="content-title">
<!--			<div class="content-title-icon"><div><div></div></div></div>-->
			<div>You recently visited</div>
		</div>

		<div>
			<ul>
				<? foreach($myVisits['data'] as $connection) : ?>
					<li>
						<?= View::factory('parts/userava-more', array(
							'isTooltip' => FALSE,
							'isCustomInfo' => TRUE,
							'avasize' => 'avasize_52',
							'ouser' => $connection
						)); ?>
					</li>
				<? endforeach; ?>
			</ul>
		</div>
	</div>
<? endif; ?>

