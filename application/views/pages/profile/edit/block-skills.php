<? // dump($items_skills, 1); ?>
<? // dump($skill_endorsement, 1); ?>
<? // dump($isConnected, 1); ?>
<? // dump($isEdit, 1); ?>

<?
	$isEmpty = TRUE;
	if (!empty($items_skills['data'])) {
		$isEmpty = FALSE;
	}
	if(!isset($isConnected)) {
		$isConnected = FALSE;
	}
?>

<div class="block-skills is-edit">
	<div class="profile-title">Skills & Endorsements
		<? if (isset($isEdit) && $isEdit) : ?>
			<a href="<?= Request::generateUri('profile', ($isEmpty) ? 'addSkills' : 'editSkills') ?>"
			   onclick="web.blockProfileEdit(); return web.ajaxGet(this);"
			   class="btn-roundblue-border icons <?= ($isEmpty) ? 'i-addcustom' : 'i-editcustom' ?>  "
			   title="<?= ($isEmpty) ? 'Add' : 'Edit' ?> skills"><span></span><?= ($isEmpty) ? 'Add' : 'Edit' ?></a>
		<? endif; ?>
	</div>

	<ul class="skills_block_inner">

		<? foreach ($items_skills['data'] as $skill) : ?>
			<?= View::factory('pages/profile/edit/item-skills', array(
				'skill'             => $skill,
				'skill_endorsement' => $skill_endorsement,
				'isConnected'       => $isConnected
			)) ?>
		<? endforeach; ?>

	</ul>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		web.initUserGallery('.user-gallery');
	});
</script>