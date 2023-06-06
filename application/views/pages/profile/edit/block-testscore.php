<?// dump($items_testscores, 1); ?>
<?// dump($isEdit, 1); ?>

<div class="block-testscore is-edit">
	<div class="profile-title">Test Scopes
		<? if(isset($isEdit) && $isEdit): ?>
			<a href="<?= Request::generateUri('profile', 'addTestScore')?>" class="btn-roundblue-border icons i-addcustom"  onclick="web.blockProfileEdit(); return web.ajaxGet(this);" title="Add test score"><span></span>Add</a>
		<? endif; ?>
	</div>
	<ul class="item-list">
		<? foreach ($items_testscores['data'] as $item) : ?>
			<li data-id="<?= $item->id ?>">
				<div>
					<div class="bg-grey">
						<?= Html::chars($item->testscoreName); ?>
						<? if(isset($isEdit) && $isEdit): ?>
							<a href="<?= Request::generateUri('profile', 'editTestScore', $item->id)?>" onclick="web.blockProfileEdit(); return web.ajaxGet(this);"  class="btn-roundblue-border icons i-editcustom "  title="Edit test score"><span></span>Edit</a>
							<a href="<?= Request::generateUri('profile', 'removeTestScore', $item->id)?>" class="btn-roundblue-border icons i-deletecustom"  onclick="return box.confirm(this, true);" title="Delete test score"><span></span>Delete</a>
						<? endif; ?>
					</div>
					<div class="lineheight18">
						<b>Fieald of occupation: </b><?= Html::chars($item->occupation); ?><br>
						<b>Score:</b> <?= Html::chars($item->score); ?><br>
						<b>Date: </b> <?= date('m/d/Y', strtotime($item->dateScore)) ?><br>
						<? if(!empty($item->description)) : ?>
							<b>Descriptions: </b><br>
						<? endif; ?>
					</div>
					<? if(!empty($item->description)) : ?>
						<div class="testscope-text">
							<?= nl2br(Html::chars($item->description)); ?>
						</div>
					<? endif; ?>
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