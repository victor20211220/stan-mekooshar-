<?// dump($profile, 1); ?>
<?// dump($content, 1); ?>

<div class="user-upgrade_profile_payment">
	<div class="block-upgrade_profile_payment">
		<div class="upgrade_profile-about_user">
			<div class="title-big">Upgrade Profile</div>
			<div>
				<?= View::factory('parts/userava-more', array(
					'ouser' => $profile,
					'avasize' => 'avasize_52',
					'isCustomInfo' => TRUE,
					'isLinkProfile' => FALSE
				)) ?>
			</div>
			<div class="upgrade_profile-status">
				<? if($profile->updateExp != 0 && strtotime($profile->updateExp) >= (time() - 60*60*24*365*10)) : ?>
					<div class="upgrade_profile-upgrade_exp_time">
						Gold Account EXP time<br>
						<b><?= date('m.d.Y', strtotime($profile->updateExp)) ?></b>
					</div>
				<? endif; ?>
				<div class="upgrade_profile-upgrade_type">
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

		<div>
			<?= $content ?>
		</div>
	</div>
</div>