<? // dump($active, 1) ?>
<? // dump($group, 1) ?>
<? // dump($counter, 1) ?>
<? // dump($countUnchecked, 1) ?>

<div class="group-menupanel">
	<div class="filterpanel">
		<div class="content-title">
			Group menu
		</div>
		<ul class="filterpanel-menu">


			<? if ($group->discussionControlType == GROUP_DISSCUSSION_TYPE_APPROVAL || ($group->discussionControlType == GROUP_DISSCUSSION_TYPE_FREE && $countUnchecked > 0)) : ?>
				<li>
					<ul class="filterpanel-submenu active">
						<li>
							<a class="<?= (isset($active) && $active == 'check_content') ? 'active' : NULL ?>"
							   href="<?= Request::generateUri('groups', 'checkContent', $group->id) ?>">
								<span></span>
								Check content
								<? if($countUnchecked != 0) : ?>
									<div class="filterpanel-counter menu-counter menupanel-contents">
										<span data-count="<?= $countUnchecked ?>">(<?= ($countUnchecked != 0) ? $countUnchecked : '' ?>)</span></div>
								<? endif; ?>
							</a>
						</li>
					</ul>
				</li>
			<? endif; ?>

			<li>
				<a href="#"
				   class="menu-item <?= (in_array($active, array('members_admin', 'members_user', 'members_requests'))) ? 'icon-down' : 'icon-next' ?>"
				   onclick="return web.showHideFilterMenu(this);"><span></span>Members
					<? if($counter != 0) : ?>
						<div class="filterpanel-counter menu-counter menupanel-requests">
							<span data-count="<?= $counter ?>">(<?= ($counter != 0) ? $counter : '' ?>)</span></div>
					<? endif; ?>
				</a>
				<ul class="filterpanel-submenu <?= (in_array($active, array('members_admin', 'members_user', 'members_requests'))) ? 'active' : NULL ?>">

					<li>
						<a href="<?= Request::generateUri('groups', 'membersAdmin', $group->id) ?>"
						   class="<?= (isset($active) && $active == 'members_admin') ? 'active' : NULL ?>"><span></span>Administration</a>
					</li>
					<li>
						<a href="<?= Request::generateUri('groups', 'membersUser', $group->id) ?>"
						   class="<?= (isset($active) && $active == 'members_user') ? 'active' : NULL ?>"><span></span>Members</a>
					</li>
					<? if ($group->accessType == GROUP_ACCES_TYPE_APPROVAL || ($group->accessType == GROUP_ACCES_TYPE_FREE && $counter > 0)) : ?>
						<li>
							<a href="<?= Request::generateUri('groups', 'membersRequest', $group->id) ?>"
							   class="<?= (isset($active) && $active == 'members_requests') ? 'active' : NULL ?>"><span></span>Requests
								<? if($counter != 0) : ?>
									<div class="filterpanel-counter menu-counter menupanel-requests">
										 <span data-count="<?= $counter ?>">(<?= ($counter != 0) ? $counter : '' ?>)</span></div>
								<? endif; ?>
							</a>
						</li>
					<? endif ?>
				</ul>
			</li>
			<li>
				<ul class="filterpanel-submenu active">
					<li>
						<a href="<?= Request::generateUri('groups', 'settings', $group->id) ?>"
						   class="menu-item <?= (isset($active) && $active == 'settings') ? 'active' : NULL ?>"><span></span>Settings</a>
					</li>
				</ul>
			</li>
			<? if ($group->user_id == $user->id) : ?>
				<li>
					<ul class="filterpanel-submenu active">
						<li>
							<a href="<?= Request::generateUri('groups', 'changeOwner', $group->id) ?>"
							   class="menu-item <?= (isset($active) && $active == 'change_owner') ? 'active' : NULL ?>"><span></span>Change
								owner</a>
						</li>
					</ul>
				</li>
				<li>
					<ul class="filterpanel-submenu active">
						<li>
							<a href="<?= Request::generateUri('groups', 'removeGroup', $group->id) ?>"
							   class="menu-item <?= (isset($active) && $active == 'delete_group') ? 'active' : NULL ?>"><span></span>Delete
								group</a>
						</li>
					</ul>
				</li>
			<? endif; ?>

		</ul>
	</div>

</div>