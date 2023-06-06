<?// dump($items_certifications, 1); ?>
<?// dump($isEdit, 1); ?>

<div class="block-certifications is-edit">
	<div class="profile-title">Certifications
		<? if(isset($isEdit) && $isEdit) : ?>
			<a href="<?= Request::generateUri('profile', 'addCertification')?>" class="btn-roundblue-border icons i-addcustom "  onclick="web.blockProfileEdit(); return web.ajaxGet(this);" title="Add sertificat"><span></span>Add</a>
		<? endif; ?>
	</div>
	<ul class="item-list">
		<? foreach ($items_certifications['data'] as $item) : ?>


			<?
			$authorityName = Html::chars(trim($item->authorityName));
//			$number = Html::chars(trim($item->number));
			$url = Html::chars(trim($item->url));

			$date = '';

			if($item->isCurrent == 1) {
				if(strtotime($item->dateFrom) >= 1) {
					$date = date('m/Y', strtotime($item->dateFrom)) . ' - current (This certificate does not expire)';
				} else {
					$date = 'This certificate does not expire';
				}
			} else{
				if(strtotime($item->dateFrom) >= 1 && strtotime($item->dateTo) >= 1) {
					$date = date('m/Y', strtotime($item->dateFrom)) . ' - ' . $date = date('m/Y', strtotime($item->dateTo));
				} elseif (strtotime($item->dateFrom) >= 1) {
					$date = ' from ' . date('m/Y', strtotime($item->dateFrom));
				} elseif (strtotime($item->dateTo) >= 1) {
					$date = ' to ' . date('m/Y', strtotime($item->dateTo));
				}
			}
			?>



			<li data-id="<?= $item->id ?>">
				<div>
					<div class="bg-grey">
						<?= Html::chars($item->certificationName); ?>
						<? if(isset($isEdit) && $isEdit) : ?>
							<a href="<?= Request::generateUri('profile', 'editCertification', $item->id)?>" onclick="web.blockProfileEdit(); return web.ajaxGet(this);"  class="btn-roundblue-border icons i-editcustom "  title="Edit certificat"><span></span>Edit</a>
							<a href="<?= Request::generateUri('profile', 'removeCertification', $item->id)?>" class="btn-roundblue-border icons i-deletecustom "  onclick="return box.confirm(this, true);" title="Delete certificat"><span></span>Delete</a>
						<? endif; ?>
					</div>
					<div>
						<? if(!empty($authorityName)) : ?>
							<span class="text-line"><b>Certification authority: </b><?= $authorityName ?></span>
						<? endif; ?>
						<? if(!empty($url)) : ?>
							<a class="icons i-link icon-round-min icon-text" href="<?= $url ?>" title="" target="_blank"><span></span><?= $url ?></a>
						<? endif; ?>
						<? if(!empty($date)) : ?>
							<span class="text-line"><b>Dates: </b><?= $date ?><br></span>
						<? endif; ?>
					</div>
				</div>
			</li>
		<? endforeach; ?>
		</ul>
</div>