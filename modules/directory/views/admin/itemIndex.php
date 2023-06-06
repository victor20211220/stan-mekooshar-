<div class="content-header">
	<div class="back_button">
		<a eva-content="Go back" class="btn btn-left" title="Go back" href="<?=Request::$controller . 'browse/' . $section  . '/' . $item->parentId . '/?show=' . ($item->isCategory ? 'categories' : 'items') ?>">Back</a>
	</div>
	
	<? if(!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false; $title = ($title) ? $title[0] : 'Dashboard';
	} ?>
	
	<h1><?=Text::short($title) ?></h1>
	
	<div class="content-nav">
		<? if($itemPermissions['edit']) : ?>
		<a eva-content="Edit text" class="main-btn main-btn-text  <?=($showed == 'text') ? 'active' : '' ?>" href="<?=Request::$controller . 'editItem/' . $section . '/' . $item->id . '/' ?>"><span>Text</span></a>
		<? endif; ?>
		
		<? if(isset($modSettings['images'])) : ?>
		<a eva-content="Manage images" class="main-btn main-btn-images <?=($showed == 'images') ? 'active' : '' ?>" href="<?=Request::$uri . '?edit=images' ?>"><span>Images</span></a>
		<? endif; ?>
		
		<? if(isset($modSettings['attachments'])) : ?>
		<a eva-content="Manage files" class="main-btn main-btn-attachments2 <?=($showed == 'attachments') ? 'active' : '' ?>" href="<?=Request::$uri . '?edit=attachments' ?>"><span><span>Attachments</span></span></a>
		<? endif; ?>
		
		<? if(isset($modSettings['videos'])) : ?>
		<a eva-content="Manage video" class="main-btn main-btn-videos <?=($showed == 'videos') ? 'active' : '' ?>" href="<?=Request::$uri . '?edit=videos' ?>"><span>Videos</span></a>
		<? endif; ?>
		
		<? if(isset($modSettings['audios'])) : ?>
		<a eva-content="Manage audio files" class="main-btn main-btn-audios <?=($showed == 'audios') ? 'active' : '' ?>" href="<?=Request::$uri . '?edit=audios' ?>"><span>Audios</span></a>
		<? endif; ?>
	</div>
	
	<? if($itemData['permissions']['delete']) : ?>
	<div class="remove-btn">
		<a eva-content="Remove item" eva-confirm="" class="btn btn-remove" href="<?=Request::$controller . 'removeItem/' . $section . '/' . $item->id . '/' ?>">Remove</a>
	</div>
	<? endif; ?>
</div>

<div class="content-data">
	<div class="files-block">
	<? if($showed == 'images') : ?>
		<? if($itemData['permissions']['images']['add']) : ?>
		<div class="add-image">
			<a class="item-edit-image" href="<?=Request::$controller . 'addImage/' . $section . '/' . $item->id . '/' ?>">
				<span class="type-image item-type-plus">Add image</span>
			</a>
		</div>
		<? endif; ?>

		<?if (isset($items) && count($items)) : ?>
			<ul class="<?=$showed ?>-list sortable">
				<?foreach ($items as $image):?>
					<li id="<?=$showed ?>_<?=$image->id ?>">
						<div class="thumb">
							<a eva-content="See image" rel="gallery" title="<?=Html::chars($image->name) ?>" class="gallery" href="<?=Model_Directoryimage::src($image, 'fullsize') ?>">
								<img src="<?=Model_Directoryimage::src($image, 'tiny') ?>" alt="<?=Html::chars($image->name) ?>"/>
							</a>
						</div>
						<div class="nav">
							<? if(isset($modSettings['images']['sorting']) && $modSettings['images']['sorting']) : ?>
								<span class="sortable-handler nav-btn main-btn-move"><span>Move</span></span>
							<?endif?>
							<? if(isset($modSettings['images']['actions']['edit']) && $modSettings['images']['actions']['edit']) : ?>
								<a eva-content="Edit image" class="item-edit-image nav-btn main-btn-edit" href="<?=Request::$controller . 'editImage/' . $section . '/' . $image->id . '/' ?>"><span>Edit</span></a>
							<? endif ?>
							<?if(isset($modSettings['images']['actions']['delete']) && $modSettings['images']['actions']['delete']) : ?>
								<a eva-content="Remove image" eva-confirm="" class=" nav-btn main-btn-remove" href="<?=Request::$controller . 'removeImage/' . $section . '/' . $image->id . '/' ?>"><span>Remove</span></a>
							<?endif?>
							<? if($image->hotspots) : ?>
								<a eva-confirm="" class="btn-edit btn-edit-hotspots" href="<?=Request::$controller . 'hotspots/' . $section . '/' . $image->id . '/' ?>">Remove</a>
							<?endif?>
						</div>
					</li>
				<?endforeach?>
			</ul>
		<?endif?>
	<? elseif($showed == 'attachments') : ?>
		<? if($itemData['permissions'][$showed]['add']) : ?>
		<a href="<?=Request::$controller . 'addAttachment/' . $section . '/' . $item->id . '/' ?>" class="add-item item-edit-attachment">
			<span class="item-type-plus type-attachment"></span>
			<span class="item-title">
				Add Attachment
			</span>
		</a>
		<? endif; ?>
		
		<?if (isset($items) && count($items)):?>
		<ul class="items-list <?=$showed ?>-list <?=($sorting) ? 'sortable-y' : '' ?>">
			<? foreach ($items as $item) : ?>
				<li id="<?=$showed ?>_<?=$item->id ?>">
					<? if($sorting) : ?>
					<div class="sortable-handler"></div>
					<? endif; ?>
					<div class="item">
						<div class="item-actions">
							<? if(isset($modSettings[$showed]['actions']['edit']) && $modSettings[$showed]['actions']['edit']) : ?>
								<a  eva-content="Edit file" class="nav-btn item-edit-attachment main-btn-edit" href="<?=Request::$controller . 'editAttachment/' . $section . '/' . $item->id . '/' ?>"><span>Edit</span></a>
							<? endif ?>
							<?if(isset($modSettings[$showed]['actions']['delete']) && $modSettings[$showed]['actions']['delete']) : ?>
								<a eva-content="Remove file" eva-confirm="" class="nav-btn main-btn-remove" href="<?=Request::$controller . 'removeAttachment/' . $section . '/' . $item->id . '/' ?>"><span>Remove</span></a>
							<?endif?>
						</div>
						<div class="item-type"></div>
						<div class="item-title">
							<a eva-content="Download file" target="_blank" href="<?=Model_Directoryattachment::src($item); ?>">
								<?=Text::short(Html::chars($item->name), 60) ?> (<?=Html::chars($item->filename) ?>)
							</a>
						</div>
					</div>
				</li>
			<?endforeach?>
		</ul>
		<? endif; ?>
	<? elseif($showed == 'videos') : ?>
		<? if(isset($modSettings['videos']['preview'])) : ?>
			<? $size = $modSettings['videos']['preview']; ?>
			<? if($itemData['permissions'][$showed]['add']) : ?>
			<div class="add-video">
				<a class="item-edit-video" href="<?=Request::$controller . 'addVideo/' . $section . '/' . $item->id . '/' ?>">
					<span class="type-video item-type-plus">Add video</span>
				</a>
			</div>
			<? endif; ?>

			<?if (isset($items) && count($items)) : ?>
			<ul class="images-list <?=$showed ?>-list sortable">
				<? foreach ($items as $video) : ?>
				<li id="<?=$showed ?>_<?=$video->id ?>">
					<div class="thumb">
						<a eva-content="See video" title="<?=Html::chars($video->name) ?>" class="video" href="<?=Model_Directoryvideo::url($video) ?>">
							<img src="<?=Model_Directoryvideo::src($video, $size) ?>" alt="<?=Html::chars($video->name) ?>"/>
						</a>
					</div>
					<div class="nav">
						<? if(isset($modSettings['videos']['sorting']) && $modSettings['videos']['sorting']) : ?>
							<span class="sortable-handler nav-btn main-btn-move"><span>Move</span></span>
						<? endif ?>
						<? if(isset($modSettings['videos']['actions']['edit']) && $modSettings['videos']['actions']['edit']) : ?>
							<a eva-content="Edit video" class="item-edit-video nav-btn main-btn-edit" href="<?=Request::$controller . 'editVideo/' . $section . '/' . $video->id . '/' ?>"><span>Edit</span></a>
						<? endif ?>
						<? if(isset($modSettings['videos']['actions']['delete']) && $modSettings['videos']['actions']['delete']) : ?>
							<a eva-content="Remove video" eva-confirm="" class="nav-btn main-btn-remove" href="<?=Request::$controller . 'removeVideo/' . $section . '/' . $video->id . '/' ?>"><span>Remove</span></a>
						<? endif ?>
					</div>
				</li>
				<? endforeach ?>
			</ul>
			<? endif ?>
		<? else : ?>
			<? if($itemData['permissions'][$showed]['add']) : ?>
			<a href="<?=Request::$controller . 'addVideo/' . $section . '/' . $item->id . '/' ?>" class="add-item item-edit-video">
				<span class="item-type-plus type-video"></span>
				<span class="item-title">
					Add Video
				</span>
			</a>
			<? endif; ?>

			<?if (isset($items) && count($items)):?>
			<ul class="items-list <?=$showed ?>-list <?=($sorting) ? 'sortable-y' : '' ?>">
				<? foreach ($items as $item) : ?>
					<li id="<?=$showed ?>_<?=$item->id ?>">
						<? if($sorting) : ?>
						<div class="sortable-handler"></div>
						<? endif; ?>
						<div class="item">
							<div class="item-actions">
								<? if(isset($modSettings[$showed]['actions']['edit']) && $modSettings[$showed]['actions']['edit']) : ?>
									<a eva-content="Edit video" class="nav-btn item-edit-video main-btn-edit" href="<?=Request::$controller . 'editVideo/' . $section . '/' . $item->id . '/' ?>"><span>Edit</span></a>
								<? endif ?>
								<?if(isset($modSettings[$showed]['actions']['delete']) && $modSettings[$showed]['actions']['delete']) : ?>
									<a eva-content="Remove video" eva-confirm="" class="nav-btn main-btn-remove" href="<?=Request::$controller . 'removeVideo/' . $section . '/' . $item->id . '/' ?>"><span>Remove</span></a>
								<?endif?>
							</div>
							<div class="item-type"></div>
							<div class="item-title">
								<a eva-content="See video" title="<?=Html::chars($item->name) ?>" class="video" href="<?=Model_Directoryvideo::url($item) ?>">
									<?=Text::short(Html::chars($item->name), 35) ?> (<?=Model_Directoryvideo::url($item) ?>)
								</a>
							</div>
						</div>
					</li>
				<?endforeach?>
			</ul>
			<? endif; ?>
		<? endif ?>
	<? elseif($showed == 'audios') : ?>
		<? if($itemData['permissions'][$showed]['add']) : ?>
		<a href="<?=Request::$controller . 'addAudio/' . $section . '/' . $item->id . '/' ?>" class="add-item item-edit-audio">
			<span class="item-type-plus type-audio"></span>
			<span class="item-title">
				Add audio
			</span>
		</a>
		<? endif; ?>

		<?if (isset($items) && count($items)):?>
		<ul class="items-list <?=$showed ?>-list <?=($sorting) ? 'sortable-y' : '' ?>">
			<?foreach ($items as $item):?>
				<li id="<?=$showed ?>_<?=$item->id ?>">
					<? if($sorting) : ?>
					<div class="sortable-handler"></div>
					<? endif; ?>
					<div class="item">
						<div class="item-actions">
							<? if(isset($modSettings[$showed]['actions']['edit']) && $modSettings[$showed]['actions']['edit']) : ?>
								<a eva-content="Edit audio" class="nav-btn item-edit-audio main-btn-edit" href="<?=Request::$controller . 'editAudio/' . $section . '/' . $item->id . '/' ?>"><span>Edit</span></a>
							<? endif ?>
							<?if(isset($modSettings[$showed]['actions']['delete']) && $modSettings[$showed]['actions']['delete']) : ?>
								<a eva-content="Remove audio" eva-confirm="" class="nav-btn main-btn-remove" href="<?=Request::$controller . 'removeAudio/' . $section . '/' . $item->id . '/' ?>"><span>Remove</span></a>
							<?endif?>
						</div>
						<div class="item-type"></div>
						<div class="item-title">
							<a eva-content="Download audio file" href="<?=Model_Directoryaudio::src($item); ?>">
								<?=Text::short(Html::chars($item->name), 60) ?> (<?=Html::chars($item->filename) ?>)
							</a>
						</div>
					</div>
				</li>
			<?endforeach?>
		</ul>
		<? endif; ?>
	<? else : ?>

	<? endif; ?>
	</div>
</div>
<div class="content-footer">
	<span class="status">
	<? if(count($items)) : ?>
		Total: <span class="showed"><?=count($items) ?></span> <?=$showed ?>
	<? else : ?>
		No <?=$showed ?>
	<? endif; ?>
	</span>
</div>