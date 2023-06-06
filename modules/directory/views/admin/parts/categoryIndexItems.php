<?if ($showed == 'categories') : ?>
	<? foreach($items as $item) : ?>
		<li id="<?=$showed . '_' . $item->id?>">
			<? if ($permisions['sorting']): ?>
			<div class="sortable-handler"></div>
			<? endif; ?>
			
			<div class="item">
				<div class="item-actions">
					<? if ($permisions['edit']): ?>
					<a eva-content="Edit text" class="nav-btn main-btn-text" href="<?=Request::$controller . 'editCategory/' . $section . '/' . $item->id . '/' ?>"><span>Edit</span></a>
					<? endif; ?>
					
					<? if (isset($modSettings['images'])): ?>
					<a eva-content="Manage images" class="nav-btn main-btn-images" href="<?=Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=images' ?>"><span>Images</span></a>
					<? endif; ?>
					
					<? if (isset($modSettings['attachments'])): ?>
					<a eva-content="Manage files" class="nav-btn main-btn-attachments" href="<?=Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=attachments' ?>"><span>Attachments</span></a>
					<? endif; ?>
					
					<? if (isset($modSettings['videos'])): ?>
					<a eva-content="Manage attachments" class="nav-btn main-btn-videos" href="<?=Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=videos' ?>"><span>Videos</span></a>
					<? endif; ?>
					
					<? if (isset($modSettings['audios'])): ?>
					<a eva-content="Manage audios" class="nav-btn main-btn-audios" href="<?=Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=audios' ?>"><span>Audios</span></a>
					<? endif; ?>
					
					<? if ($permisions['delete']): ?>
					<a eva-content="Remove item" class="nav-btn main-btn-remove" eva-confirm="" href="<?=Request::$controller . 'removeCategory/' . $section . '/' . $item->id . '/' ?>"><span>Remove</span></a>	
					<? endif; ?>
				</div>
				<div class="item-type"></div>
				<div class="item-title">
					<?=Html::anchor(Request::$controller . 'browse/' . $section . '/' . $item->id . '/', ($item->$contentTitle) ? Html::chars($item->$contentTitle) : '<i>Category</i>') ?>
				</div>

			</div>
		</li>
	<?endforeach?>
<? elseif($showed == 'items') : ?>
	<? foreach($items as $item) : ?>
		<li id="<?=$showed . '_' . $item->id ?>">
			<? if ($permisions['sorting']): ?>
			<div class="sortable-handler"></div>
			<? endif; ?>
			
			<div class="item">
				<div class="item-actions">
					<? if ($permisions['edit']): ?>
					<a eva-content="Edit text" class="nav-btn main-btn-text" href="<?=Request::$controller . 'editItem/' . $section . '/' . $item->id . '/' ?>"><span>Edit</span></a>
					<? endif; ?>
					
					<? if (isset($modSettings['images'])): ?>
					<a eva-content="Manage images" class="nav-btn main-btn-images" href="<?=Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=images' ?>"><span>Images</span></a>
					<? endif; ?>
					
					<? if (isset($modSettings['attachments'])): ?>
					<a eva-content="Manage files" class="nav-btn main-btn-attachments" href="<?=Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=attachments' ?>"><span>Attachments</span></a>
					<? endif; ?>
					
					<? if (isset($modSettings['videos'])): ?>
					<a eva-content="Manage video" class="nav-btn main-btn-videos" href="<?=Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=videos' ?>"><span>Videos</span></a>
					<? endif; ?>
					
					<? if (isset($modSettings['audios'])): ?>
					<a eva-content="Manage audio files" class="nav-btn main-btn-audios" href="<?=Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=audios' ?>"><span>Audios</span></a>
					<? endif; ?>
					
					<? if ($permisions['delete']): ?>
					<a eva-content="Remove item" class="nav-btn main-btn-remove" eva-confirm="" href="<?=Request::$controller . 'removeItem/' . $section . '/' . $item->id . '/' ?>"><span>Remove</span></a>	
					<? endif; ?>
				</div>
				<div class="item-type"></div>
				<div class="item-title">
					<span><?=$item->$contentTitle ? Html::chars($item->$contentTitle) : '<i>Item</i>' ?></span>
				</div>
			</div>
		</li>
	<?endforeach?>
<? endif; ?>