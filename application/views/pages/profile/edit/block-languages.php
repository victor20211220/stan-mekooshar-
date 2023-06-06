<? // dump(#items_languages, 1) ?>
<? // dump(#isEdit, 1) ?>

<?
	$isEmpty = TRUE;
	if (!empty($items_languages['data'])) {
		$isEmpty = FALSE;
	}
?>

<div class="block-landuages is-edit">
	<div class="profile-title">Languages
		<? if (isset($isEdit) && $isEdit): ?>
			<a href="<?= Request::generateUri('profile', ($isEmpty) ? 'addLanguage' : 'editLanguage') ?>"
			   onclick="web.blockProfileEdit(); return web.ajaxGet(this);"
			   class="btn-roundblue-border icons <?= ($isEmpty) ? 'i-addcustom' : 'i-editcustom' ?>  "
			   title="<?= ($isEmpty) ? 'Add' : 'Edit' ?> languages"><span></span><?= ($isEmpty) ? 'Add' : 'Edit' ?></a>
		<? endif; ?>
	</div>
	<ul class="landuages-bloks">
		<? foreach ($items_languages['data'] as $language) : ?>
			<li><?= Html::chars($language->languageName) ?>
				<span><?= t('language_level.' . $language->levelType) ?></span></li>
		<? endforeach; ?>
	</ul>
</div>