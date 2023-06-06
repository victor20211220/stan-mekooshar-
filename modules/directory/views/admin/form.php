<div class="content-header">
	<div class="back_button">
		<a eva-content="Go back" class="btn btn-left" title="Go back" href="<?= $backUrl ?>">Back</a>
	</div>

	<h1><?
		$title = array_pop($crumbs);
		echo Text::short($title[0])
		?></h1>

	<? if ($item) : ?>
		<div class="content-nav">
			<a eva-content="Edit text" class="main-btn main-btn-text  <?= ($showed == 'text') ? 'active' : '' ?>" href="<?= Request::$controller . 'editItem/' . $section . '/' . $item->id . '/' ?>"><span>Text</span></a>

			<? if (isset($modSettings['images'])) : ?>
				<a eva-content="Manage images" class="main-btn main-btn-images <?= ($showed == 'images') ? 'active' : '' ?>" href="<?= Request::$controller . 'item/' . $section . '/' . $item->id . '/' . '?edit=images' ?>"><span>Images</span></a>
			<? endif; ?>

			<? if (isset($modSettings['attachments'])) : ?>
				<a eva-content="Manage files" class="main-btn main-btn-attachments2 <?= ($showed == 'attachments') ? 'active' : '' ?>" href="<?= Request::$controller . 'item/' . $section . '/' . $item->id . '/' . '?edit=attachments' ?>"><span><span>Attachments</span></span></a>
			<? endif; ?>

			<? if (isset($modSettings['videos'])) : ?>
				<a eva-content="Manage video" class="main-btn main-btn-videos <?= ($showed == 'videos') ? 'active' : '' ?>" href="<?= Request::$controller . 'item/' . $section . '/' . $item->id . '/' . '?edit=videos' ?>"><span>Videos</span></a>
			<? endif; ?>

			<? if (isset($modSettings['audios'])) : ?>
				<a eva-content="Manage audio files" class="main-btn main-btn-audios <?= ($showed == 'audios') ? 'active' : '' ?>" href="<?= Request::$controller . 'item/' . $section . '/' . $item->id . '/' . '?edit=audios' ?>"><span>Audios</span></a>
			<? endif; ?>
		</div>

		<? if (isset($itemData) && $itemData['permissions']['delete']) : ?>
			<div class="remove-btn">
				<a eva-content="Remove item" eva-confirm="" class="btn btn-remove" href="<?= Request::$controller . 'removeItem/' . $section . '/' . $item->id . '/' ?>">Remove</a>
			</div>
		<? endif; ?>
	<? endif; ?>
</div>
<div class="content-data">
	<div class="content-inner">
		<div class="content-data-inner">
			<?
			if (isset($form)) {
				echo  $form->header();
				if (isset($multiUpload) && $multiUpload) {
					echo new View('admin/parts/filesUploader', array('type' => $type, 'section' => $section, 'itemId' => $itemId, 'maxSize' => $maxSize, 'ext' => $ext));
					?>
					<div class="uploaded_files"></div>
					<?
				} else {
					echo $form->render('default');
					echo $form->render('customFieldset');
				}
				echo  $form->elements['submit']->render();
				echo  $form->footer();
			}
			?>

		</div>
	</div>
</div>
<div class="content-footer">

</div>