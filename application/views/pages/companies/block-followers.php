<?// dump($followers, 1); ?>

<div class="block-followers">
	<div class="title-big">Followers</div>
	<ul>
		<? foreach($followers['data'] as $follower): ?><li>
				<?=  View::factory('parts/userava-more', array(
					'isCustomInfo' => TRUE,
					'isTooltip' => FALSE,
					'avasize' => 'avasize_52',
					'ouser' => $follower
				)); ?>
		</li><? endforeach ?>
	</ul>
</div>