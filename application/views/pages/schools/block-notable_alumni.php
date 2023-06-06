<?// dump($notableAlumni, 1); ?>

<? $i = 0; ?>

<div class="block-notable_alumni">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>Notable alumni</div>
	</div>

	<? if(isset($notableAlumni) && !empty($notableAlumni['data'])) : ?>
		<div class="notable_alumni-students">
			<div class="user-gallery">
				<div class="btn-prev icon-prev" onclick="web.prevUserGallery(this);"><span></span></div><ul><li>
						<? foreach($notableAlumni['data'] as $alumni) :
							$i++;
							echo View::factory('parts/userava-more', array(
								'isCustomInfo' => FALSE,
								'isTooltip' => TRUE,
								'avasize' => 'avasize_44',
								'ouser' => $alumni,
								'keyId' => 'userId'
							));
							if(($i % 5) == 0 && ($i < count($notableAlumni['data']))) :
								echo('</li><li>');
							endif;
						endforeach; ?>
					</li></ul><div class="btn-next icon-next" onclick="web.nextUserGallery(this);"><span></span></div>
			</div>
		</div>
	<? else: ?>
		<div class="list-item-empty">
			No notable alumni
		</div>
	<? endif; ?>
</div>


<script type="text/javascript">
	$(document).ready(function(){
		web.initUserGallery('.user-gallery');
	});
</script>