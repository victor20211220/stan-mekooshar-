<? // dump($tags, 1); ?>
<? // dump($companies, 1); ?>
<? // dump($regions, 1); ?>
<? // dump($query, 1); ?>
<? // dump($countReceived, 1); ?>

<? $countries = t('countries'); ?>
<? unset($_GET['tag'], $_GET['company'], $_GET['region']) ?>

<div class="connections-leftpanel">
	<? if (Request::$action == 'index') : ?>
		<div class="filterpanel">
			<div class="content-title">
				My connections
			</div>
			<a class="filter-title-btn" href="<?= Request::generateUri('connections', 'index'); ?>">Show all</a>

			<ul class="filterpanel-menu">
				<li>
					<a href="<?= Request::generateUri('connections', 'manageTags'); ?>" onclick="return box.load(this);"
					   class="btn-roundblue-border icons i-editcustom"><span></span>edit</a>
					<a href="#"
					   class="menu-item <?= (isset($query['tag']) && $query['tag']) ? 'icon-down' : 'icon-next' ?>"
					   onclick="return web.showHideFilterMenu(this);"><span></span>Tags</a>
					<ul class="filterpanel-submenu <?= (isset($query['tag']) && $query['tag']) ? 'active' : NULL ?>">

						<? if ($tags): ?>
							<? foreach ($tags['data'] as $tag) : ?>
								<li>
									<a class="<?= (isset($query['tag']) && $query['tag'] == $tag->id) ? 'active' : NULL; ?>"
									   href="<?= Request::generateUri('connections', 'index') . Request::getQuery('tag', $tag->id) ?>"><?= $tag->name ?></a>
								</li>
							<? endforeach; ?>
						<? endif; ?>

					</ul>
				</li>
				<li>
					<a href="#"
					   class="menu-item <?= (isset($query['company']) && $query['company']) ? 'icon-down' : 'icon-next' ?>"
					   onclick="return web.showHideConnectionsMenu(this);"><span></span>Companies</a>
					<ul class="filterpanel-submenu <?= (!empty($query['company'])) ? 'active' : NULL; ?>">

						<? if ($companies): ?>
							<? foreach ($companies['data'] as $company) : ?>
								<li>
									<a class="<?= (isset($query['company']) && (($query['company'] == 'c' . $company->companyId) || ($query['company'] == 'u' . $company->universityId))) ? 'active' : NULL; ?>"
									   href="<?= Request::generateUri('connections', 'index') . Request::getQuery('company', !empty($company->companyName) ? 'c' . $company->companyId : 'u' . $company->universityId) ?>"><?= !empty($company->companyName) ? $company->companyName : $company->universityName ?></a> <? // 	TODO ?>
								</li>
							<? endforeach; ?>
						<? endif; ?>
					</ul>
				</li>


				<li>
					<a href="#"
					   class="menu-item <?= (isset($query['region']) && $query['region']) ? 'icon-down' : 'icon-next' ?>"
					   onclick="return web.showHideConnectionsMenu(this);"><span></span>Region</a>
					<ul class="filterpanel-submenu <?= (!empty($query['region'])) ? 'active' : NULL; ?>">

						<? if ($regions): ?>
							<? foreach ($regions['data'] as $region) : ?>
								<? if (!empty($region->userCountry)) : ?>
									<li>
										<a class="<?= (isset($query['region']) && $query['region'] == $region->userCountry) ? 'active' : NULL; ?>"
										   href="<?= Request::generateUri('connections', 'index') . Request::getQuery('region', $region->userCountry) ?>"><?= $countries[$region->userCountry] ?></a>
									</li>
								<? endif; ?>
							<? endforeach; ?>
						<? endif; ?>
					</ul>
				</li>
			</ul>


		</div>
	<? else: ?>
		<div class="filterpanel">
			<div class="content-title">
				My invitations
			</div>
			<ul class="filterpanel-menu">
				<li>
					<ul class="filterpanel-submenu active">

						<li>
							<a class="icons i-received icon-text <?= (Request::$action == 'receivedInvitations') ? 'active' : NULL ?>"
							   href="<?= Request::generateUri('connections', 'receivedInvitations') ?>">
								<span></span>Received
								<? if (isset($countReceived) && $countReceived != 0) : ?>
									<div class="filterpanel-counter connections-countreceived counter-body">(<span
											data-count="<?= $countReceived ?>"><?= $countReceived ?></span>)</div>
								<? endif; ?>
							</a>
						</li>
						<li>
							<a class="icons i-received icon-text <?= (Request::$action == 'sentInvitations') ? 'active' : NULL ?>"
							   href="<?= Request::generateUri('connections', 'sentInvitations') ?>">
								<span></span>
								Sent
							</a>
						</li>

					</ul>
				</li>
			</ul>
		</div>
	<? endif; ?>
</div>

