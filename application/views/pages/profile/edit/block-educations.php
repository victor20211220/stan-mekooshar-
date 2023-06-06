<?// dump($items_educations, 1); ?>
<?// dump($isEdit, 1); ?>

<div class="block-educations is-edit">
	<div class="profile-title">Education
		<? if(isset($isEdit) && $isEdit) : ?>
			<a href="<?= Request::generateUri('profile', 'addEducation')?>"  onclick="web.blockProfileEdit(); return web.ajaxGet(this);"  class="btn-roundblue-border icons i-addcustom" title="Add user education"><span></span>Add</a>
		<? endif; ?>
	</div>
		<ul class="item-list">
			<? foreach ($items_educations['data'] as $item) : ?>
				<li data-id="<?= $item->id ?>">
					<div>
						<div class="lineheight18">
							<b><?= Html::chars($item->universityName) ?></b>
							<? if(isset($isEdit) && $isEdit) : ?>
								<a href="<?= Request::generateUri('profile', 'editEducation', $item->id)?>"  onclick="web.blockProfileEdit(); return web.ajaxGet(this);"  class="btn-roundblue-border icons i-editcustom" title="Edit user education"><span></span>Edit</a>
								<a href="<?= Request::generateUri('profile', 'removeEducation', $item->id)?>" class="btn-roundblue-border icons i-deletecustom "  onclick="return box.confirm(this, true);" title="Delete education"><span></span>delete</a>
							<? endif; ?>
							<? if(!empty($item->yearFrom) || !empty($item->yearTo)) : ?>
								<br>Attended:
							<? endif; ?>
							<?= (!empty($item->yearFrom)) ? ' from: ' . Html::chars($item->yearFrom) : null ?>
							<?= (!empty($item->yearTo)) ? ' to: ' . Html::chars($item->yearTo) : null ?>

							<?= (!empty($item->fieldOfStudy)) ? '<br>Field of study: ' . Html::chars($item->fieldOfStudy) : null ?>
							<?= (!empty($item->degree)) ? '<br>Degree: ' . Html::chars($item->degree) : null ?>
							<?= (!empty($item->grade)) ? '<br>Grade: ' . Html::chars($item->grade) : null ?>
						</div>
						<? if(!empty($item->activitiesAndSocieties)) : ?>
							<div class="bg-grey">Activities and societies</div>
							<div class="educations-text">
								<?= nl2br(Html::chars($item->activitiesAndSocieties)); ?>
							</div>
						<? endif; ?>
<!--                        todo uncomment -->
<!--						--><?// if(!empty($item->description)) : ?>
<!--							<div class="bg-grey">Descriptions</div>-->
<!--							<div class="educations-text">-->
<!--								--><?//= nl2br(Html::chars($item->description)); ?>
<!--							</div>-->
<!--						--><?// endif; ?>

					</div>
				</li>
			<? endforeach; ?>
		</ul>
</div>