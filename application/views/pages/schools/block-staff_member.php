<?// dump($staffMember, 1); ?>

<? $i = 0; ?>

<div class="block-staff_member">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>A faculty of staff member</div>
	</div>

	<? if(isset($staffMember) && !empty($staffMember['data'])) : ?>
		<div class="staff_member-member">
			<div class="user-gallery">
				<div class="btn-prev icon-prev" onclick="web.prevUserGallery(this);"><span></span></div><ul><li>
						<? foreach($staffMember['data'] as $member) :
							$i++;
							echo View::factory('parts/userava-more', array(
								'isCustomInfo' => FALSE,
								'isTooltip' => TRUE,
								'avasize' => 'avasize_44',
								'ouser' => $member,
								'keyId' => 'userId'
							));
							if(($i % 5) == 0 && ($i < count($staffMember['data']))) :
								echo('</li><li>');
							endif;
						endforeach; ?>
					</li></ul><div class="btn-next icon-next" onclick="web.nextUserGallery(this);"><span></span></div>
			</div>
		</div>
	<? else: ?>
		<div class="list-item-empty">
			No staff member
		</div>
	<? endif; ?>
</div>


<script type="text/javascript">
	$(document).ready(function(){
		web.initUserGallery('.user-gallery');
	});
</script>