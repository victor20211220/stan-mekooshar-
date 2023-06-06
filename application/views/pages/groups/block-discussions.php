<?// dump($f_Updates_AddUpdate, 1); ?>
<?// dump($timelinesGroup, 1); ?>
<?// dump($group, 1); ?>

<div class="block-discussions">
	<div class="title-big">Summary</div>
<!--	<div class="group_description_short">-->
<!--		--><?//= $group->descriptionShort ?>
<!--		<a href="#" onclick="return web.showGroupMoreDiscription();">Show more</a>-->
<!--	</div>-->
	<div class="group_description">
		<?= $group->description ?>
	</div>
	<ul class="discussions-group_info">
		<? if(!empty($group->website)) : ?>
			<li>
				<div>Website link</div>
				<div>
					<a class="discussions-group_link" href="<?= $group->website ?>" title="Website link" target="_blank"><?= $group->website ?></a>
				</div>
			</li>
		<? endif; ?>

		<? if(!empty($group->ownerEmail)) : ?>
			<li>
				<div>Email address</div>
				<div><?= $group->ownerEmail ?></div>
			</li>
		<? endif; ?>
	</ul>
	<div class="discussions-group_before_update">
		<div class="discussion-title">
			<div class="title-big">
				Discussions
			</div>
			<a class="btn-roundblue <?= (!Request::get('isPopular', FALSE)) ? 'active' : null ?>" href="<?= Request::generateUri('groups', $group->id) ?>">Recent</a>
			<a class="btn-roundblue <?= (Request::get('isPopular', FALSE)) ? 'active' : null ?>" href="<?= Request::generateUri('groups', $group->id) . Request::getQuery('isPopular', 1) ?>">Popular</a>
		</div>
	</div>

	<? if($f_Updates_AddUpdate) : ?>
		<div>
			<? $f_Updates_AddUpdate->form->header() ?>
			<a href="#" class="icons i-close hidden upload-cancel_load"><span></span></a>
			<? $f_Updates_AddUpdate->form->render('fields') ?>
			<div class="loader hidden">Loading...</div>
			<? $f_Updates_AddUpdate->form->render('urldata') ?>
			<div class="updates-file hidden">
				<a href="#" class="updates-file-link" target="_blank"></a>
				<a href="#" class="icons i-close upload-cancel_pdfdoc" onclick="return web.updateClear($(this).closest('form'));" ><span></span></a>
			</div>
			<div class="upload-images user-gallery hidden">
				<div class="upload-image_text">Only 1 photo can be attached</div>
				<div class="btn-prev icon-prev" onclick="web.prevUserGallery(this);"><span></span></div><ul class="uploader-list uploaderAddUpdate">
					<li class="hidden"></li>
				</ul><div class="btn-next icon-next" onclick="web.nextUserGallery(this);"><span></span></div>
			</div>
			<? $f_Updates_AddUpdate->form->render('submit') ?>
			<? $f_Updates_AddUpdate->form->footer() ?>
		</div>
	<? endif ?>

	<div class="block-list-discussion block-list-updates">

		<? if(count($timelinesGroup['data']) != 0) : ?>



			<ul class="list-items">
				<? if($timelinesGroup) : ?>
					<li class="hidden"></li>
					<? foreach($timelinesGroup['data'] as $timeline) : ?>
						<?= View::factory('pages/updates/item-update', array(
							'timeline' => $timeline,
							'isUsernameLink' => TRUE,
							'textLen' => 200,
							'showTimelineType' => FALSE
						)) ?>
					<? endforeach; ?>
					<li>
						<?= View::factory('common/default-pages', array(
								'isBand' => TRUE,
								'autoScroll' => TRUE
							) + $timelinesGroup['paginator']) ?>
					</li>
				<? endif; ?>
			</ul>
		<? else : ?>
			<ul class="list-items">
				<li class="list-item-empty">
					No discussions
				</li>
			</ul>
		<? endif; ?>
	</div>
</div>

<? if($f_Updates_AddUpdate) : ?>
	<?= View::factory('parts/file-uploader', array(
		'initFunction' => 'contentUploaderList',
		'parentId' => 0,
		'type' => FILE_UPDATES,
		'multiple' => FALSE
	)); ?>

	<script type="text/javascript">
		editorGallery.addImg($('.uploader-list > .hidden'));
		$('#addupdate-text').on('keyup input',function(){
			web.updateAddUrl(this);
		});
		setTimeout(function(){
			$('.qq-uploader input[type="file"]').removeAttr('multiple');
		}, 400);
	</script>
<? endif ?>