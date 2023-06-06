<!DOCTYPE html>
<!--[if IE 7 ]> <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]> <!--> <html xmlns="https://www.w3.org/1999/xhtml" class="no-js" xml:lang="en" lang="en"> <!-- <![endif]-->
<head>
	<?

		// If title is not defined then it will contain last part of breadcrumbs.
		if(!isset($title)) {
			$lastCrumb = count($crumbs) ? end($crumbs) : false;
			$title = $lastCrumb ? $lastCrumb[0] : false;
		}
		
		// Draw head section
		View::factory('common/default-head', array(
			'title'   => $title,
			'description' => (isset($description)) ? $description : '',
			'keywords' => (isset($keywords)) ? $keywords : '',
			'links'   => $links,
			'scripts' => $scripts
		))->render();
	?>
</head>
<body class="<?=Request::getUserAgent('mobile') == 'iPad' ? 'mobile' : '' ?>">
	<div class="body-content-inner">
		<div class="main-container">
			<div class="header-outer">
				<div class="header">
					<div class="logo">
						<a class="btn btn-home" href="/admin/"></a>
					</div>
					<div class="nav">
						<ul>
							<li>Hi, <?=ucfirst($user->firstName)?> !</li>
							<!--						<li>
														<a class="btn-info eva-icon" href="http://ukietech.com/"><span>Ukietech corp.</span></a>

													</li>-->
							<li>
								<a eva-content="Website homepage" target="_blank" class="btn-info home-icon" href="/"><span>Website homepage</span></a>

							</li>
							<li>
								<a eva-content="Log out" class="btn-info logout-icon" href="/auth/logout/"><span>Log Out</span></a>
							</li>
						</ul>
						<span id="ajax-message"><? if(!empty($messages)) { $message = array_pop($messages); echo $message[0]; } ?></span>
						<a class="ukietech-logo" eva-content="Ukietech corp." href="#">Ukietech.com</a>
					</div>

					<? if(!empty($crumbs) && count($crumbs) > 1) : ?>
						<div class="crumbs">
							<?=Html::crumbs($crumbs, '<span>&rarr;</span>'); ?>
						</div>
					<? endif; ?>
				</div>
			</div>
			<div class="content-outer">
				<div class="content">
					<div class="content-box-outer">
						<div class="content-box">
							<div class="left-block">
								<div class="left-block-inner">
									<div class="website-settings">
										<a eva-title="Website settings" eva-content="Go to Website settings to change general settings (i.e. website title, keywords etc.)" class="main-btn main-btn-settings <?=$active == 'settings' ? 'active' : '' ?>" href="/admin/settings/"><span>Settings</span></a>
									</div>
									<div class="website-modules">
										<?=new View('admin/nav', array('active' => $active)); ?>
									</div>
								</div>
							</div>
							<div class="center-block">
								<?=$content?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?=isset($dbg) ? $dbg : '' ?>
</body></html>