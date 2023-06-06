<?// dump($isEdit, 1); ?>
<?// dump($profile, 1); ?>

<?
$isEmpty = true;
if(!empty($profile->summaryText)) {
	$isEmpty = false;
}
?>

<div class="block-summary is-edit">
	<div class="profile-title">Summary
		<? if(isset($isEdit) && $isEdit) : ?>
			<a href="<?= Request::generateUri('profile', ($isEmpty) ? 'addSummary' : 'editSummary')?>" class="btn-roundblue-border icons <?= ($isEmpty) ? 'i-addcustom' : 'i-editcustom' ?>  "  onclick="web.blockProfileEdit(); return web.ajaxGet(this);" title="<?= ($isEmpty) ? 'Add' : 'Edit' ?> summary"><span></span><?= ($isEmpty) ? 'Add' : 'Edit' ?></a>
		<? endif; ?>
	</div>
	<div class="summary-text">
		<?= nl2br(HTML::chars($profile->summaryText)) ?>
	</div>
</div>