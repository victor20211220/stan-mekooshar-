<?// dump($countNewJobApplicant, 1); ?>
<?// dump($countGroupNewMember, 1); ?>
<?// dump($countGroupNewContent, 1); ?>
<?// dump($countSchoolNewStaffMember, 1); ?>
<?
$countNewInteresets = $countGroupNewMember + $countGroupNewContent + $countSchoolNewStaffMember;
$countNewGroup = $countGroupNewMember + $countGroupNewContent;
?>

<div class="userpanel-inner">
	<ul class="userpanel-left">
		<li>
			<a class="<?= (isset($subactive) && $subactive == 'updates') ? 'active' : null ?>" href="<?= Request::generateUri('updates', 'index') ?>" title="Updates">Updates</a>
		</li><li>
			<a class="<?= (isset($subactive) && $subactive == 'profile') ? 'active' : null ?>" href="<?= Request::generateUri('profile', 'index') ?>" title="Profile">Profile</a>
		</li><li class="menu-interests <?= ($countNewInteresets > 0) ? 'is-counter' : null ?>">
			<a class="icon-down <?= (isset($subactive) && in_array($subactive, array('schools', 'groups', 'companies'))) ? 'active' : null ?>" onclick="return web.userpanelPopup(this);" href="#" title="Interests">Interests<? if($countNewInteresets > 0) : ?><div class="userpanel-counter" data-count="<?= $countNewInteresets ?>"><?= ($countNewInteresets > 9) ? '+9' : $countNewInteresets ?></div><? endif; ?><span></span></a>
			<ul class="userpanel-sub-menu hidden">
				<li><a class="<?= (isset($subactive) && $subactive == 'schools') ? 'active' : null ?>" href="<?= Request::generateUri('schools', 'updates') ?>">SCHOOLS <? if($countSchoolNewStaffMember > 0) : ?><span class="userpanel-counter" data-count="<?= $countSchoolNewStaffMember ?>"><?= ($countSchoolNewStaffMember > 9) ? '+9' : $countSchoolNewStaffMember ?></span><? endif; ?></a></li>
				<li><a class="<?= (isset($subactive) && $subactive == 'groups') ? 'active' : null ?>" href="<?= Request::generateUri('groups', 'joined') ?>">GROUPS <? if($countNewGroup > 0) : ?><span class="userpanel-counter" data-count="<?= $countNewGroup ?>"><?= ($countNewGroup > 9) ? '+9' : $countNewGroup ?></span><? endif; ?></a></li>
				<li><a class="<?= (isset($subactive) && $subactive == 'companies') ? 'active' : null ?>" href="<?= Request::generateUri('companies', 'updates') ?>">COMPANIES</a></li>
			</ul>
		</li><li>
				<a class="menu-jobs <?= (isset($subactive) && $subactive == 'jobs') ? 'active' : null ?>" href="<?= Request::generateUri('jobs', 'index') ?>" title="Jobs">Jobs <? if($countNewJobApplicant > 0) : ?><span class="userpanel-counter" data-count="<?= $countNewJobApplicant ?>"><?= ($countNewJobApplicant > 9) ? '+9' : $countNewJobApplicant ?></span><? endif; ?></a>
		</li>
	</ul>
	<ul class="userpanel-control userpanel-right">
		<li>
			<a class="<?= (isset($subactive) && $subactive == 'messages') ? 'active' : null ?>" href="<?= Request::generateUri('messages', 'index') ?>" title="Messages"><div class="icons i-messageswhite"><span></span></div><? if($countNewMessages > 0) : ?><span class="userpanel-counter" data-count="<?= $countNewMessages ?>"><?= ($countNewMessages > 9) ? '+9' : $countNewMessages ?></span><? endif; ?></a>
		</li><li>
			<a class="<?= (isset($subactive) && $subactive == 'invite') ? 'active' : null ?>" href="<?= Request::generateUri('connections', 'invite') ?>" title="My invitations"><div class="icons i-invite invites-button-div"><span></span><strong class="invites-button-title">Invites</strong></div></a>
		</li><li>
			<? if($countNewConnections > 0) : ?>
				<a class="<?= (isset($subactive) && $subactive == 'connections') ? 'active' : null ?>" href="<?= Request::generateUri('connections', 'receivedInvitations')?>" title="Connections"><div class="icons i-user"><span></span></div><? if($countNewConnections > 0) : ?><span class="userpanel-counter userpanel-new_connections" data-count="<?= $countNewConnections ?>"><?= ($countNewConnections > 9) ? '+9' : $countNewConnections ?></span><? endif; ?></a>
			<? else: ?>
				<a class="<?= (isset($subactive) && $subactive == 'connections') ? 'active' : null ?>" href="<?= Request::generateUri('connections', 'index')?>" title="Connections"><div class="icons i-user"><span></span></div><? if($countNewConnections > 0) : ?><span class="userpanel-counter userpanel-new_connections" data-count="<?= $countNewConnections ?>"><?= ($countNewConnections > 9) ? '+9' : $countNewConnections ?></span><? endif; ?></a>
			<? endif ?>
		</li><li>
			<a class="notificationBtn notification-btn" href="#" title="Notifications" onclick="return web.showHideNotifications(this);"><div class="icons i-notification"><span></span></div><? if($countNotifications > 0) : ?><span class="userpanel-counter" data-count="<?= $countNotifications ?>"><?= ($countNotifications > 9) ? '+9' : $countNotifications ?></span><? endif; ?></a>
		</li><li>
			<a class="icon-down <?= (isset($subactive) && in_array($subactive, array('upgrade', 'privacy'))) ? 'active' : null ?>" onclick="return web.userpanelPopup(this);" href="#" title="Settings">Settings<span></span></a>
			<ul class="userpanel-sub-menu hidden">
				<li><a class="" href="<?= Request::generateUri('auth', 'logout'); ?>" title="Logout">Sign out</a></li>
<!--				<li><a class="" href="--><?//= Request::generateUri('profile', 'upgrade') ?><!--">Upgrade profile</a></li>-->
				<li><a href="<?= Request::generateUri('profile', 'privacySettings') ?>">Privacy & settings</a></li>
				<? if(in_array($user->role, array(USER_TYPE_WEBROOT, USER_TYPE_WEBADMIN))) : ?>
					<li><a href="<?= Request::generateUri('admin', 'index') ?>">Admin panel</a></li>
				<? endif; ?>
			</ul>
		</li>
	</ul>
</div>