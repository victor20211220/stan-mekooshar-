<?// dump($profile, 1); ?>
<?// dump($f_Profile_PrivacySettings, 1); ?>
<?// dump($blockedUsers, 1); ?>

<div class="user-privacy_settings">
	<div class="block-privacy_settings">
		<div class="privacy_settings-about_user">
			<div class="title-big">Privacy & Settings</div>
			<div>
				<?= View::factory('parts/userava-more', array(
					'ouser' => $profile,
					'avasize' => 'avasize_52',
					'isCustomInfo' => TRUE,
					'isLinkProfile' => FALSE
				)) ?>
			</div>
			<div class="privacy_settings-status">
				<? if($profile->updateExp != 0 && strtotime($profile->updateExp) >= (time() - 60*60*24*365*10)) : ?>
					<div class="privacy_settings-upgrade_exp_time">
						Gold Account EXP time<br>
						<b><?= date('m.d.Y', strtotime($profile->updateExp)) ?></b>
					</div>
				<? endif; ?>
				<div class="privacy_settings-upgrade_type">
					Your Profile status<br>
					<span class="title-middle">
						<? if($profile->accountType == ACCOUNT_TYPE_GOLD) : ?>
							<span class="upgrade_profile-upgrade_type_gold">GOLD ACCOUNT</span>
						<? else: ?>
							BASIC ACCOUNT
						<? endif; ?>
					</span>
				</div>
			</div>
		</div>
		<div class="privacy_settings-joined">
			Joined on <?= date('m-d-Y', strtotime($profile->createDate)) ?>
			<? if($profile->accountType == ACCOUNT_TYPE_BASIC) : ?>
				<span>Get more opportunities with the Gold plan!</span>
				<a class="blue-btn icon-next" href="<?= Request::generateUri('profile', 'upgrade') ?>"><span></span>Upgrade profile now!</a>
			<? endif; ?>

		</div>

		<?= $f_Profile_PrivacySettings->form->header(); ?>
        <div class="privacy_settings-privacy_settings">
				<div class="title-big">Privacy settings</div>
				<div class="privacy_settings_left_privacy">
					<?= $f_Profile_PrivacySettings->form->render('fields2'); ?>
				</div>
				<div class="privacy_settings_right_privacy">
					<?= $f_Profile_PrivacySettings->form->render('fields3'); ?>
				</div>
			</div>

			<div class="privacy_settings-block_users">
				<div class="title-big">Bloked users</div>
				<a class="icons i-add icon-text" href="<?= Request::generateUri('profile', 'addBlockUser') ?>" onclick="return box.load(this);"><span></span>add user</a>
				<ul class="list-items">
					<li class="hidden"></li>
					<? foreach($blockedUsers['data'] as $user): ?>
						<?= View::factory('pages/profile/item-blocked_user', array(
							'user' => $user
						)) ?>
					<? endforeach; ?>
				</ul>
			</div>

		<?= $f_Profile_PrivacySettings->form->render('submit'); ?>
		<?= $f_Profile_PrivacySettings->form->footer(); ?>
	</div>
</div>