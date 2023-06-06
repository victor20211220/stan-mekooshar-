<?// dump($profile, 1); ?>

<div class="user-upgrade_profile">
	<div class="block-upgrade_profile">
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
				<? if($profile->updateExp != 0 && strtotime($profile->updateExp) > (time() - 60*60*24*365*10)) : ?>
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

		<table border="0" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<td>Active options</td>
					<td>Basic plan</td>
					<td>GOLD PLAN</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><div class="upgrade_profile-grey_backgraund">Send messages</div></td>
					<td><div class="upgrade_profile-grey_backgraund">Restricted to 3rd connection</div></td>
					<td><div class="upgrade_profile-brown_backgraund"><div class="icons i-accessblue"><span></span></div></div></td>
				</tr>
				<tr>
					<td><div class="upgrade_profile-grey_backgraund">View other profiles</div></td>
					<td><div class="upgrade_profile-grey_backgraund">Restricted to 3rd connection</div></td>
					<td><div class="upgrade_profile-brown_backgraund"><div class="icons i-accessblue"><span></span></div></div></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td>FREE FOR ALL
						<? if($profile->accountType == ACCOUNT_TYPE_GOLD) : ?>
							<a class="btn-roundblue" href="<?= Request::generateUri('profile', 'disupgradeProfile') ?>" onclick="return box.confirm(this, true)">Disupgrade<span></span></a>
						<? endif; ?>
					</td>
					<td>$ 10.99 / MONTH<a class="btn-roundblue" href="<?= Request::generateUri('commerce', 'upgradeProfile') ?>">Upgrade now<span></span></a></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>