<?// dump(isEdit, 1); ?>
<?// dump($profile, 1); ?>

<?
$isEmpty = true;
if(!empty($profile->interests) || !empty($profile->maritalStatus) || !empty($profile->birthdayDate)) {
	$isEmpty = false;
}
?>

<div class="block-addition_information is-edit">
	<div class="profile-title">Additional information
		<? if(isset($isEdit) && $isEdit) : ?>
			<a href="<?= Request::generateUri('profile', 'editAdditional')?>"  onclick="web.blockProfileEdit(); return web.ajaxGet(this);"   class="btn-roundblue-border icons <?= ($isEmpty) ? 'i-addcustom' : 'i-editcustom' ?> " title="<?= ($isEmpty) ? 'Add' : 'Edit' ?> edditional information"><span></span><?= ($isEmpty) ? 'Add' : 'Edit' ?></a>
		<? endif; ?>
	</div>
	<div class="block-addition_information-inner">
		<? if(!empty($profile->interests)) : ?>
			<div class="bg-grey">Interests</div>
			<div class="addition_information-text">
				<?= nl2br(Html::chars($profile->interests)); ?>
			</div>
		<? endif; ?>
		<? if(!empty($profile->birthdayDate) || !empty($profile->maritalStatus)) : ?>
			<div class="bg-grey">Personal Details</div>
			<div class="lineheight18">
				<? if(!empty($profile->birthdayDate)) : ?>
					<b>Birthday: </b><?= date('m/d/Y', strtotime($profile->birthdayDate)) ?><br>
				<? endif; ?>
				<? if(!empty($profile->maritalStatus)) : ?>
					<b>Marital status: </b><?= t('maritel_status.' . $profile->maritalStatus) ?><br>
				<? endif; ?>
			</div>
		<? endif; ?>
	</div>
</div>