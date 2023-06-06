<?// dump($f_findInProfile, 1); ?>
<?// dump($items_connections, 1); ?>
<?// dump($profile, 1); ?>
<?
	$isShowConnections = Model_Connections::checkAllowMeToProffileConnections($profile, USER_LEVEL_ACCESS_SHOW_CONNECTIONS);
?>


<? $form = $f_findInProfile->form ?>
<div class="block-all_connections">
	<? if($isShowConnections) : ?>
		<div class="bg-blue bg-brown">
			<div class="text-bgtitle">connections &nbsp;&nbsp; <span><?= $items_connections['paginator']['count'] ?></span></div>
			<div>
				<? $form->header(); ?>
				<? $form->render('fields'); ?>
				<? $form->render('submit'); ?>
				<? $form->footer(); ?>
			</div>
		</div>

		<div>
			<ul class="block-all_connections-connections"><? foreach($items_connections['data'] as $connection) :
						echo View::factory('pages/profile/view/item-profile_connections', array(
							'connection' => $connection
						));
					endforeach; ?><li>
					<?= View::factory('common/default-pages', array(
							'controller' => Request::generateUri('profile', 'getListProfileConnection', $profile->id),
							'isBand' => TRUE,
							'autoScroll' => FALSE
						) + $items_connections['paginator']) ?>
					</li>
			</ul>

<!--			<div class="gallery-navigation">-->
<!--				<div class="btn-prev icon-prev" onclick="web.prevUserGallery(this);"><span></span>Prev</div>-->
<!--				<div class="btn-next icon-next" onclick="web.nextUserGallery(this);">Next<span></span></div>-->
<!--			</div>-->
		</div>
	<? else : ?>
		<div class="list-item-empty">
			Connections info is private
		</div>
	<? endif; ?>

</div>
<!--<script type="text/javascript">-->
<!--	$(document).ready(function(){-->
<!--		web.initUserGallery('.user-gallery');-->
<!--	});-->
<!--</script>-->

