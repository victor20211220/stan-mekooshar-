<?// dump($followSchools, 1); ?>

<? $i = 0; ?>

<div class="block-following_schools">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>Schools you are following</div>
	</div>

	<? if(isset($followSchools) && !empty($followSchools['data'])) : ?>
		<div class="following_schools-schools">
			<div class="user-gallery">
				<div class="btn-prev icon-prev" onclick="web.prevUserGallery(this);"><span></span></div><ul><li><? foreach($followSchools['data'] as $school) :
							$i++;
							echo View::factory('parts/schoolava-more', array(
								'school' => $school,
								'avasize' => 'avasize_44',
								'isLinkProfile' => TRUE,
								'isTooltip' => TRUE
							));
							if(($i % 10) == 0 && ($i < count($followSchools['data']))) :
								echo('</li><li>');
							endif;
						endforeach; ?></li></ul><div class="btn-next icon-next" onclick="web.nextUserGallery(this);"><span></span></div>
			</div>
		</div>
	<? else: ?>
		<div class="list-item-empty">
			No following schools
		</div>
	<? endif; ?>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		web.initUserGallery('.user-gallery');
	});
</script>