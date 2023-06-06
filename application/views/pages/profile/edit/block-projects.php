<?// dump($items_projects, 1); ?>
<?// dump($isEdit, 1); ?>

<div class="block-projects is-edit">
	<div class="profile-title">Projects
		<? if(isset($isEdit) && $isEdit): ?>
			<a href="<?= Request::generateUri('profile', 'addProject')?>" class="btn-roundblue-border icons i-addcustom "  onclick="web.blockProfileEdit(); return web.ajaxGet(this);" title="Add project"><span></span>Add</a>
		<? endif; ?>
	</div>
	<ul class="item-list">
		<? foreach ($items_projects['data'] as $item) : ?>
			<li data-id="<?= $item->id ?>">
				<div>
					<div class="bg-grey">
						<?= Html::chars($item->projectName); ?>
						<? if(isset($isEdit) && $isEdit): ?>
							<a href="<?= Request::generateUri('profile', 'editProject', $item->id)?>" onclick="web.blockProfileEdit(); return web.ajaxGet(this);"  class="btn-roundblue-border icons i-editcustom " title="Edit project"><span></span>Edit</a>
							<a href="<?= Request::generateUri('profile', 'removeProject', $item->id)?>" class="btn-roundblue-border icons i-deletecustom "  onclick="return box.confirm(this, true);" title="Delete project"><span></span>Delete</a>
						<? endif; ?>
					</div>
					<div class="lineheight18">
						<? if(!empty($item->universityName)) : ?>
							<b>Field of occupation:</b><?= Html::chars($item->universityName) ?> <br>
						<? else: ?>
							<b>Field of occupation:</b><?= Html::chars($item->companyName) ?><br>
						<? endif; ?>
						<b>Date:</b><?= date('m/Y', strtotime($item->dateFrom)) ?> - <?= ($item->isCurrent == 1) ? 'current' : date('m/Y', strtotime($item->dateTo)) ?><br>
						<? if(!empty($item->description)) : ?>
							<b>Description:</b><br>
						<? endif; ?>
					</div>
<!--                    todo uncomment description -->
<!--					--><?// if(!empty($item->description)) : ?>
<!--						<div class="projects-text">-->
<!--							--><?//= nl2br(Html::chars($item->description)) ?>
<!--						</div>-->
<!--					--><?// endif; ?>
					<? if(!empty($item->url)) : ?>
						<div>
							<a class="icons i-link icon-round-min icon-text" href="<?= Html::chars($item->url) ?>" title="" target="_blank"><span></span><?= Html::chars($item->url) ?></a>
						</div>
					<? endif; ?>
				</div>
			</li>
		<? endforeach; ?>
	</ul>
</div>