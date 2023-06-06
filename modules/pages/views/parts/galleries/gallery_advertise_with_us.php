<?// dump($items, 1); ?>
<?// dump($group, 1); ?>
<?
	$i = 0;
?>

<? if(count($items) != 0): ?>
	<ul class="gallery-advertise-photo">
		<? foreach($items as $item): ?>
			<? if($item->parent_id == $group): ?>
				<? $i++; ?>
				<li>
					<div class="advertise-photo">
						<? $preview_url = Model_Files::generateUrl($item->token, $item->ext, $item->type, $item->isImage, $item->name, 'preview_adv'); ?>
						<? list($imgWidth, $imgHeight, $imgType, $imgAttr) = getimagesize(realpath(NULL) . $preview_url); ?>
						<img src="<?= Model_Files::generateUrl($item->token, $item->ext, $item->type, $item->isImage, $item->name, 'advertise') ?>"/>
						<div  onclick="box.previewImage(this);" data-imgwidth="<?= $imgWidth ?>" data-imgheight="<?= $imgHeight ?>"  data-img="<?= $preview_url ?>" >
							<div class="advertise-photo-hoverinfo">
								<div>Preview</div>
							</div>
						</div>
					</div>
				</li>
			<? endif; ?>
		<? endforeach; ?>
	</ul>
<? endif; ?>
