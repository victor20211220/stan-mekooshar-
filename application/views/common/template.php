<!DOCTYPE html>
<!--[if IE 7 ]> <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]> <!--> <html class="no-js" lang="en"> <!-- <![endif]-->
	<head>
		<meta name="msapplication-config" content="none">
		<?
			// If title is not defined then it will contain last part of breadcrumbs.
			$title = isset($title) ? $title : (count($crumbs) ? array_shift(end($crumbs)) : false);
			// Prepend home breadcrumb
			array_unshift($crumbs, array('Home', '/'));
			// Draw head section
			View::factory('common/default-head', array(
				'title'   => (count($crumbs) > 1) ? array(array_shift($title)) : $title,
				'description' => (isset($description)) ? $description : '',
				'keywords' => (isset($keywords)) ? $keywords : '',
				'links'   => $links,
				'scripts' => $scripts
			))->render();
		?>
	</head>
	<body class="<?= (isset($active) && $active == 'profile') ? null : 'is-notlogined' ?> <?= (isset($page)) ? $page : null ?>">
		<div class="panel-top">
			<? if(isset($active) && $active=='home') : ?>
				<div class="home-nav">
					<div class="container">
						<div class="row">
							<div class="col-md-12 col-sm-8 logo-block">
								<a href="/">
									<img src="/resources/images/logo-home.png" alt="Mekooshar" width="400"/>
								</a>
							</div>
							<div class="col-md-12  col-sm-16 social-block">
<!--								<div class="login-block">-->
<!--									<a class="menu-login home-login --><?//= (isset($active) && $active=='login') ? 'active' : null ?><!--" href="#" title="Login" onclick="return web.showHideLogin(this);">Login</a>-->
<!--								</div>-->
								<div class="share">
									<img src="/resources/images/home-nav/share.png" alt="share"/>
									<span>Share with friends</span>
									<a href="javascript: void(0) " onclick='window.open("https://www.facebook.com/sharer/sharer.php?u=<?= 'https://' . $_SERVER['SERVER_NAME'];  ?>", "Facebook", "width=626,height=436" );' class="facebook"></a>
									<a href="javascript: void(0) "  onclick='window.open("http://twitter.com/share?text=<?= $title; ?>;url=<?= 'https://' . $_SERVER['SERVER_NAME'];  ?>", "twitter", "toolbar=0,status=0,width=548,height=325");'class="twitter"></a>
									<a target="_blank" href="https://www.linkedin.com/cws/share?url=<?= 'https://' . $_SERVER['SERVER_NAME'];  ?>" class="in"></a>
									<a href="javascript: void(0) " onclick="window.open('https://plus.google.com/share?url=<?= 'https://' . $_SERVER['SERVER_NAME'];  ?>', 'Google Plus', 'width=800,height=300');" class="google"></a>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 col-nd-offset-12 col-sm-16 col-sm-offset-8">
								<div class="panel-loginform panelLogin" style="position: relative; top: 12px; left: 0">
									<?= $f_login->render(); ?>
								</div>
							</div>
						</div>
					</div>



					<? if(isset($user_panel)) : ?>
						<div class="userpanel-outer">
							<?= $user_panel ?>
						</div>
					<? endif; ?>
				</div>

			<? else : ?>
				<div class="panel-top-innser">
					<ul class="panel-top-menu">
						<? if(isset($active) && $active == 'profile') : ?>
							<li>
								<?= $f_findpanel->header() ?>
								<?= $f_findpanel->render('fields') ?>
								<?= $f_findpanel->render('submit') ?>
								<?= $f_findpanel->footer() ?>
							</li>


						<? else : ?>
							<!--						--><?// if(!$user) : ?><!--<li>-->
							<!--							<a class="--><?//= (isset($active) && $active=='home') ? 'active' : null ?><!--" href="/" title="Home">Home</a>-->
							<!--						</li>--><?// endif; ?><!--<li>-->
							<!--							<a class="--><?//= (isset($active) && $active=='about') ? 'active' : null ?><!--" href="--><?//= Request::generateUri('about', 'index')?><!--" title="About us">About us</a>-->
							<!--						</li><li>-->
							<!--							<a class="--><?//= (isset($active) && $active=='advertise_with_us') ? 'active' : null ?><!--" href="--><?//= Request::generateUri('advertiseWithUs', 'index')?><!--" title="Advertise with us">Advertise with us</a>--> <!--						</li><li>-->
							<!--							<a class="--><?//= (isset($active) && $active=='support') ? 'active' : null ?><!--" href="--><?//= Request::generateUri('support', 'index')?><!--" title="Support">Support</a>-->
							<!--						</li>-->
							<li>
							<? if(!$user) : ?>
<!--								<a class="menu-login btn-roundblue_big --><?//= (isset($active) && $active=='login') ? 'active' : null ?><!--" href="#" title="Login" onclick="return web.showHideLogin(this);">Login</a>-->
							<? else: ?>
								<a class="menu-login btn-roundblue_big" href="<?= Request::generateUri('profile', 'index')?>" title="Profile">Profile</a>
							<? endif; ?>
							</li><? endif; ?>
					</ul>

					<? if(isset($user) && $user) : ?>
						<a href="<?= Request::generateUri('updates', 'index') ?>">
							<img src="/resources/images/logo-small.png" alt="Mekooshar" width="410"   />
						</a>
					<? else: ?>
						<a href="/">
							<img src="/resources/images/logo-small.png" alt="Mekooshar"  width="410" />
						</a>
					<? endif ?>
					<div class="panel-loginform panelLogin">
						<?= $f_login->render(); ?>
					</div>
					<? if(isset($user_panel)) : ?>
						<div class="userpanel-outer">
							<?= $user_panel ?>
						</div>
					<? endif; ?>
				</div><div class="panel-top-widthfix"></div>
			<? endif; ?>

		</div>


		<div class="gallery-homepage galleryBackground1">
			<? if(isset($galleries) && !empty($galleries)) : ?>
				<? if(Request::getUserAgent('mobile')) : ?>
					<? $i = 0; ?>
					<? $links = ''; ?>
					<? foreach($galleries as $item) : $i++?>
						<? $links .= ',"' . Model_Files::generateUrl($item->token, $item->ext, $item->type, true, false, 'fullhd') . '" ' ?>
						<? break; ?>
					<? endforeach; ?>
					<? $links = substr($links, 1); ?>
					<script type="text/javascript">
						$.backstretch([
							<?= $links ?>
						], {duration: 10000, fade: 1});
					</script>
				<? else: ?>
					<? $i = 0; ?>
					<? $links = ''; ?>
					<? foreach($galleries as $item) : $i++?>
					<? $links .= ',"' . Model_Files::generateUrl($item->token, $item->ext, $item->type, true, false, 'fullhd') . '" ' ?>
					<? endforeach; ?>
					<? $links = substr($links, 1); ?>
					<script type="text/javascript">
						$.backstretch([
							<?= $links ?>
						], {duration: 10000, fade: 5000});
					</script>
				<? endif; ?>
			<? endif; ?>
		</div>

		<div class="content-grid" style="<?= (isset($active) && $active=='home') ? '' : 'opacity:0.2'?>" ></div>

		<div class="content-outer <?= (isset($active) && $active=='home') ? 'homepage' : null ?> <?= (isset($active) && $active=='advertise_with_us') ? 'page-advertise_with_us' : null ?> <?= (isset($active) && $active=='support') ? 'page-support' : null ?> " <?= (isset($active) && ($active=='support' || $active=='advertise_with_us')) ? 'style="background: rgba(18, 159, 205, 0.5);"' : null ?>>
<!--			--><?// if(isset($active) && $active=='home') : ?>
<!--				<div class="home-form">-->
<!--					--><?//= View::factory('pages/home-form', array('f_registration' => $f_registration))->render(); ?>
<!--				</div>-->
<!--			--><?// endif; ?>
			<? if(isset($active) && $active=='home') : ?>
				<?=isset($content) ? $content : '' ?>
				<div class="panel-bottom">
					<div class="panel-bottom-inner">
						<div class="panel-bottom-copiryght">All rights reserved by Mekooshar.com &copy; 2014-<?php echo date("Y"); ?></div>
						<ul class="panel-bottom-menu">
							<? if(!$user) : ?>
								<li>
									<a class="<?= (isset($active) && $active=='home') ? 'active' : null ?>" href="/" title="Home">Home</a>
								</li><? endif; ?>
								<li>
									<a class="<?= (isset($active) && $active=='about') ? 'active' : null ?>" href="<?= Request::generateUri('about', 'index')?>" title="About us">About us</a>
								</li>
								<li>
									<a class="<?= (isset($active) && $active=='team') ? 'active' : null ?>" href="<?= Request::generateUri('team', 'index')?>" title="Our team">Our team</a>
								</li>
								<li>
									<a class="<?= (isset($active) && $active=='policy') ? 'active' : null ?>" href="<?= Request::generateUri('policy', 'index')?>" title="Privacy policy">Privacy policy</a>
								</li>
								<li>
									<a class="<?= (isset($active) && $active=='advertise_with_us') ? 'active' : null ?>" href="<?= Request::generateUri('advertiseWithUs', 'index')?>" title="Advertise with us">Advertise with us</a>
								</li>
								<li>
									<a class="<?= (isset($active) && $active=='support') ? 'active' : null ?>" href="<?= Request::generateUri('support', 'index')?>" title="Support">Support</a>
								</li>
						</ul>
					</div>
				</div>
			<? else : ?>
				<div class="content-with-bottomwhite">
					<div class="content-inner">
											<?=isset($content) ? $content : '' ?>
					</div>
					<div class="panel-bottom">
						<div class="panel-bottom-inner">
							<div class="panel-bottom-copiryght">All rights reserved by Mekooshar.com &copy; 2014-<?php echo date("Y"); ?></div>
							<ul class="panel-bottom-menu">
								<? if(!$user) : ?><li>
									<a class="<?= (isset($active) && $active=='home') ? 'active' : null ?>" href="/" title="Home">Home</a>
									</li><? endif; ?><li>
									<a class="<?= (isset($active) && $active=='about') ? 'active' : null ?>" href="<?= Request::generateUri('about', 'index')?>" title="About us">About us</a>
								</li><li>
									<a class="<?= (isset($active) && $active=='policy') ? 'active' : null ?>" href="<?= Request::generateUri('policy', 'index')?>" title="Privacy policy">Privacy policy</a>
								</li><li>
									<a class="<?= (isset($active) && $active=='advertise_with_us') ? 'active' : null ?>" href="<?= Request::generateUri('advertiseWithUs', 'index')?>" title="Advertise with us">Advertise with us</a>
								</li><li>
									<a class="<?= (isset($active) && $active=='support') ? 'active' : null ?>" href="<?= Request::generateUri('support', 'index')?>" title="Support">Support</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			<? endif; ?>


		</div>

<!--		--><?// if(isset($notifications)) : ?>
			<?= View::factory('pages/list-notifications', array(
				'notifications' => $notifications
			)) ?>
<!--		--><?// endif; ?>

		<script type="text/javascript">
			$(document).ready(function(){
				<? if(isset($messages) && $messages !== false) : ?>
					<? $message = (string) new View('parts/pbox-form', array('title' => 'Message', 'content' => $messages[0][0])); ?>
					box.message('Message', '<?= $messages[0][0] ?>');
				<? endif; ?>
				<? if(isset($_SESSION['ajax_ret']) && !empty($_SESSION['ajax_ret'])) : ?>
					web.ajaxGet('<?= $_SESSION['ajax_ret'] ?>');
					<? unset($_SESSION['ajax_ret']); ?>
				<? endif; ?>
				<? if(isset($_SESSION['ajaxBox_ret']) && !empty($_SESSION['ajaxBox_ret'])) : ?>
					box.load('<?= $_SESSION['ajaxBox_ret'] ?>');
					<? unset($_SESSION['ajaxBox_ret']); ?>
				<? endif; ?>
			});
		</script>
		<? if(isset($messages) && $messages !== false) : ?>
			<div class="message" style="display: none"><?= $message ?></div>
		<? endif; ?>


		<div class="hidden Message">
			<?= new View('parts/pbox-form', array(
				'title' => '%title',
				'content' => '%content'
			)) ?>
		</div>


		<script type="text/javascript">
			web.initGalleryBackground();

			<? if(isset($_SESSION['resetPassword']) && $_SESSION['resetPassword']) : ?>
				box.load('<?= Request::generateUri('auth', 'newPassword') ?>');
			<? endif; ?>
			setInterval(function() {
				if(!box.removeTimer && box.$content && box.$content.closest('pbox-overlay').hasClass('opened')) {
					box.resize();
				}
			}, 200);
		</script>

		<?=isset($dbg) ? $dbg : '' ?>
	</body>
</html>
