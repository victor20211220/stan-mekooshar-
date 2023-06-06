<?// dump($connectionsMayKnow, 1); ?>
<? $i = 0; ?>

<? if(!empty($connectionsMayKnow['data'])) : ?>
	<div class="block-peopleyoumayknow">
		<div class="content-title">
<!--			<div class="content-title-icon"><div><div></div></div></div>-->
			<div>People you may know</div>
		</div>

		<div class="peopleuoumayknow-connections">
			<div class="user-gallery">
				<div class="btn-prev icon-prev" onclick="web.prevUserGallery(this);"><span></span></div><ul><li><? foreach($connectionsMayKnow['data'] as $connection) :
							$i++;
							echo View::factory('parts/userava-more', array(
								'isCustomInfo' => false,
								'isTooltip' => TRUE,
								'avasize' => 'avasize_44',
								'ouser' => $connection
							));
							if(($i % 5) == 0 && ($i < count($connectionsMayKnow['data']))) :
								echo('</li><li>');
							endif;
						endforeach; ?></li></ul><div class="btn-next icon-next" onclick="web.nextUserGallery(this);"><span></span></div>
			</div>
		</div>
	</div>
<? endif; ?>

<script type="text/javascript">
	$(document).ready(function(){
		web.initUserGallery('.user-gallery');
	});
</script>