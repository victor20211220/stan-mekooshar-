<?// dump($groupMembers, 1); ?>
<?

//$first = current($groupMembers['data']);
//for($i = 0; $i<= 25; $i++){
//	$groupMembers['data'][$i] = $first;
//}  // TODO tmp
?>

<? $i = 0; ?>

<? if(!empty($groupMembers['data'])) : ?>
	<div class="block-group_members">
		<div class="content-title">
<!--			<div class="content-title-icon"><div><div></div></div></div>-->
			<div>Group members</div>
		</div>

		<div class="group_members-connections">
			<div class="user-gallery">
				<div class="btn-prev icon-prev" onclick="web.prevUserGallery(this);"><span></span></div><ul><li>
						<? foreach($groupMembers['data'] as $connection) :
							$i++;
							echo View::factory('parts/userava-more', array(
								'isCustomInfo' => FALSE,
								'isTooltip' => TRUE,
								'avasize' => 'avasize_44',
								'ouser' => $connection
							));
							if(($i % 10) == 0 && ($i < count($groupMembers['data']))) :
								echo('</li><li>');
							endif;
						endforeach; ?>
					</li></ul><div class="btn-next icon-next" onclick="web.nextUserGallery(this);"><span></span></div>
			</div>
		</div>
	</div>
<? endif; ?>

<script type="text/javascript">
	$(document).ready(function(){
		web.initUserGallery('.user-gallery');
	});
</script>