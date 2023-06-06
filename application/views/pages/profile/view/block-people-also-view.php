<?// dump($connectionsAlsoViewed, 1); ?>

<? if(!empty($connectionsAlsoViewed['data'])) : ?>
	<div class="block-peoplealsovieved">
		<div class="content-title">
<!--			<div class="content-title-icon"><div><div></div></div></div>-->
			<div>People also viewed</div>
		</div>

		<div class="peoplealsovieved-connections">
			<ul>
				<? foreach($connectionsAlsoViewed['data'] as $connection) : ?>
					<li><?= View::factory('parts/userava-more', array(
							'isCustomInfo' => TRUE,
							'isTooltip' => FALSE,
							'avasize' => 'avasize_52',
							'ouser' => $connection
						)); ?></li>
				<? endforeach; ?>
			</ul>
		</div>
	</div>
<? endif; ?>

