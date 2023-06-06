<?// dump($items_experience); ?>
<?// dump($isEdit, 1); ?>
<div class="block-experience is-edit">
	<div class="profile-title">Experience
		<? if(isset($isEdit) && $isEdit) : ?>
			<a href="<?= Request::generateUri('profile', 'addExperience')?>" class="btn-roundblue-border icons i-addcustom "  onclick="web.blockProfileEdit(); return web.ajaxGet(this);" title="Add experience"><span></span>Add</a>
		<? endif; ?>
	</div>
	<ul class="item-list">
		<? foreach ($items_experience['data'] as $item) : ?>
			<li data-id="<?= $item->id ?>">
				<div>
					<div class="bg-blue lineheight18">
						<?
							$text = '';
							$text .= Html::chars($item->title);
							if(!empty($text)) {
								$text .= ' | ';
							}
							if(!empty($item->companyName)) {
								$text .= Html::chars($item->companyName);
							} elseif(!empty($item->universityName)) {
								$text .= Html::chars($item->universityName);
							}
							?>
						<?= $text ?>
						<? if(isset($isEdit) && $isEdit) : ?>
							<a href="<?= Request::generateUri('profile', 'editExperience', $item->id)?>" onclick="web.blockProfileEdit(); return web.ajaxGet(this);"  class="btn-roundblue-border icons i-editcustom "  title="Edit user experience"><span></span>Edit</a>
							<a href="<?= Request::generateUri('profile', 'removeExperience', $item->id)?>" class="btn-roundblue-border icons i-deletecustom "  onclick="return box.confirm(this, true);" title="Delete experience"><span></span>Delete</a>
						<? endif; ?><br>
						<?= (isset($item->location)) && !empty($item->location) ? Html::chars($item->location) . '<br>' : '' ?>
						<?
							$text = '';
							if(strtotime($item->dateFrom) != 0) {
								$text .= date('m/Y', strtotime($item->dateFrom));
							}
							if(!empty($text) && (strtotime($item->dateTo) != 0 || $item->isCurrent == 1)) {
								$text .= ' - ';
							}
							if($item->isCurrent == 1) {
								$text .= 'current';
							} elseif(strtotime($item->dateTo) != 0) {
								$text .= date('m/Y', strtotime($item->dateTo));
							}
						?>
						<? if(!empty($text)) : ?>
							<div class="text-title"><?= $text ?></div>
						<? endif ?>
					</div>
<!--                    todo uncomment -->
<!--					<div class="experience-text">-->
<!--						<p>--><?//= Html::chars($item->description) ?><!--</p>-->
<!--					</div>-->
				</div>
			</li>
		<? endforeach; ?>
	</ul>
</div>