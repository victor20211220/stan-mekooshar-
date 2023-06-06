<div class="content-data">
	<div class="items-block">
		<? if($pageACL && $pageACL->add) : ?>
			<a href="<?=Request::generateUri('admin/pages', 'addPage', $category) ?>" class="add-item">
				<span class="item-type-plus type-item"></span>
				<span class="item-title">
						Add page
				</span>
			</a>
		<? endif; ?>

		<ul class="items-list">
			<? foreach ($items['data'] as $item) : ?>
				<li>
					<div class="item">
						<div class="item-actions">
							<? if($pageACL && $pageACL->edit) : ?>
								<a eva-content="Edit text" class="nav-btn main-btn-text" href="<?=Request::generateUri('admin/pages', 'editPage', array($category, $item->id)) ?>"><span>Edit</span></a>
							<? endif; ?>
							<? if($pageACL && $pageACL->delete) : ?>
								<a eva-content="Remove item" class="nav-btn main-btn-remove" eva-confirm="" href="<?=Request::generateUri('admin/pages', 'removePage', array($category, $item->id)) ?>"><span>Remove</span></a>
							<? endif; ?>
						</div>
						<div class="item-type"></div>
						<div class="item-title">
							<span><?=$item->title ? Html::chars($item->title) : '<i>Page</i>' ?></span>
						</div>
					</div>
				</li>
			<?endforeach?>
		</ul>
	</div>
</div>
