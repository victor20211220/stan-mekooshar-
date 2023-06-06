<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
	<title><?= (!System::$debugEnabled ? '404 - Page not found' : 'Uncaught exception') ?></title>
	<link href="/resources/css/normalize.css" rel="stylesheet" type="text/css" media="screen">
	<link href="/resources/css/libs/jquery.Jcrop.css" rel="stylesheet" type="text/css">
	<link href="/resources/css/libs/bootstrap/bootstrap-select.min.css" rel="stylesheet" type="text/css">
	<link href="/resources/css/libs/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="/resources/css/libs/ui/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
	<link href="/resources/css/template.css" rel="stylesheet" type="text/css">
	<link href="/resources/css/website.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="/resources/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="/resources/js/libs/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript" src="/resources/js/libs/bootstrap/bootstrap-select.min.js"></script>
	<script type="text/javascript" src="/resources/js/libs/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="/resources/js/libs/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="/resources/js/libs/ui/jquery.ui.position.js"></script>
	<script type="text/javascript" src="/resources/js/libs/ui/jquery.ui.tooltip.js"></script>
	<script type="text/javascript" src="/resources/js/libs/ui/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="/resources/js/libs/jquery.Jcrop.min.js"></script>
	<script type="text/javascript" src="/resources/js/libs/jquery.autosize.min.js"></script>
	<script type="text/javascript" src="/resources/js/crop.js"></script>
	<script type="text/javascript" src="/resources/js/website.js"></script>
	<script type="text/javascript" src="/resources/js/system.js"></script>

	<style type="text/css">/*<![CDATA[*/
		h1 {
			display: inline-block;
			margin: 0;
			color: #c33;;
		}

		code {
			display: inline-block;
			white-space: pre;
			background-color: #eee;
			margin: 0.5em 0;
			padding: 0.5em;
		}

		code:before {
			white-space: pre-line;
		}

		.notice {
			color: #aaa;
		}

		.content-inner {
			text-align: center;
			padding: 150px 5px !important;
			min-height: inherit !important;
		}

		.content-outer p {
			padding-right: 70px;
		}

		.error-code {
			text-align: left;
			margin-top: 0px;
		}

		.error-code code {
			max-width: 917px;
		}

		/*]]>*/</style>
</head>
<body>

<div class="panel-top">
	<div class="panel-top-innser">
		<ul class="panel-top-menu">
			<? if (isset($active) && $active == 'profile') : ?>
				<li></li>
			<? else : ?><? if (!$user) : ?>
				<li>
				<a class="<?= (isset($active) && $active == 'home') ? 'active' : null ?>" href="/" title="Home">Home</a>
				</li><? endif; ?>
				<li>
				<a class="<?= (isset($active) && $active == 'about') ? 'active' : null ?>"
				   href="<?= Request::generateUri('about', 'index') ?>" title="About us">About us</a>
				</li>
				<li>
					<a class="<?= (isset($active) && $active == 'policy') ? 'active' : null ?>"
					   href="<?= Request::generateUri('policy', 'index') ?>" title="Privacy policy">Privacy policy</a>
				</li>
				<li>
				<? if (!$user) : ?>

				<? else: ?>
					<a class="menu-login" href="<?= Request::generateUri('profile', 'index') ?>"
					   title="Profile">Profile</a>
				<? endif; ?>
				</li><? endif; ?>
		</ul>
		<? if (!(isset($active) && $active == 'home')) : ?>
			<? if (isset($user) && $user) : ?>
				<a href="<?= Request::generateUri('updates', 'index') ?>">
					<img src="/resources/images/logo-small.png" alt="Mekooshar"/>
				</a>
			<? else: ?>
				<img src="/resources/images/logo-small.png" alt="Mekooshar"/>
			<? endif ?>
		<? endif; ?>
		<? if (!$user) : ?>
			<div class="panel-loginform panelLogin">
				<form action="http://localhost/signin/" class="autoform" id="login" method="post"
				      onsubmit="return box.submit(this, function(content){web.login(content)});">
					<fieldset id="login-fieldset-default">
						<ol>
						</ol>
					</fieldset>
					<fieldset class="customform" id="login-fieldset-fields">
						<ol>
							<li class="form-required">
								<div class="autoform-label"><label for="login-email">&nbsp;&nbsp;<em>*</em></label>
								</div>
								<div class="autoform-element">
									<div class="autoform-element-inner"><input required="required"
									                                           placeholder="E-mail address"
									                                           maxlength="64" tabindex="11" type="text"
									                                           id="login-email" name="login[email]">
									</div>
								</div>
							</li>
							<li class="form-required">
								<div class="autoform-label"><label for="login-password">&nbsp;&nbsp;<em>*</em></label>
								</div>
								<div class="autoform-element">
									<div class="autoform-element-inner"><input required="required"
									                                           placeholder="Password" maxlength="24"
									                                           minlength="5" tabindex="12"
									                                           type="password" id="login-password"
									                                           name="login[password]"
									                                           autocomplete="off"></div>
								</div>
							</li>
						</ol>
					</fieldset>
					<fieldset id="login-fieldset-submit">
						<ol>
							<li>
								<div class="autoform-label"><label for="login-button"><a class="btn-roundblue" href="#"
								                                                         onclick="$(this).closest('form').find('input:submit').click(); return false;">Log
											in</a>&nbsp;</label></div>
								<div class="autoform-element">
									<div class="autoform-element-inner"><span id="login-button"></span></div>
								</div>
							</li>
							<li id="login-field-submit" style="display: none;">
								<div class="autoform-label"><label for="login-submit">&nbsp;&nbsp;</label></div>
								<div class="autoform-element">
									<div class="autoform-element-inner"><input type="submit" id="login-submit"
									                                           name="login[submit]" value="Submit">
									</div>
								</div>
							</li>
						</ol>
					</fieldset>
				</form>
			</div>
		<? endif; ?>
		<? if (isset($user_panel)) : ?>
			<div class="userpanel-outer">
				<?= $user_panel ?>
			</div>
		<? endif; ?>
	</div>
	<div class="panel-top-widthfix"></div>
</div>


<ul class="gallery-homepage galleryBackground"></ul>
<div class="content-grid"></div>

<div class="content-outer <?= (isset($active) && $active == 'home') ? 'homepage' : null ?>">
	<div class="content-with-bottomwhite">
		<div class="error-blade">
			<div class="content-inner">
				<!--				--><? //=isset($content) ? $content : '' ?>
				<? if (!System::$debugEnabled): ?>
					<h1>404 - Page not found</h1>
					<p>Oh NO! Something went wrong.</p>
				<? else: ?>
					<div class="error-code">
						<h1>Uncaught exception "<?= get_class($exception) ?>"</h1>
						<p>Message: <br/>
							<code><?= $exception->getMessage() ?></code>
						</p>
						<p>Thrown in: <br/>
							<code><?= $exception->getFile() . ':' . $exception->getLine() ?></code>
						</p>
						<p>Stack trace: <br/>
							<code><?= $exception->getTraceAsString() ?></code>
						</p>
						<p class="notice">
							Processed in <?= number_format(microtime(true) - START_TIME, 4) ?> seconds,
							using <?= number_format(memory_get_usage(true) / 1048576, 2) ?> MB of memory.
						</p>
					</div>
				<? endif ?>
			</div>
		</div>
	</div>
</div>

<? if (isset($notifications)) : ?>
	<?= View::factory('pages/list-notifications', array(
			'notifications' => $notifications
	)) ?>
<? endif; ?>

<script type="text/javascript">
	$(document).ready(function () {
		<? if(isset($messages) && $messages !== false) : ?>
		<? $message = (string)new View('parts/pbox-form', array('title' => 'Message', 'content' => $messages[0][0])); ?>
		box.message('Message', '<?= $messages[0][0] ?>');
		<? endif; ?>
	});
</script>
<? if (isset($messages) && $messages !== false) : ?>
	<div class="message" style="display: none"><?= $message ?></div>
<? endif; ?>


<div class="hidden Message">
	<?= new View('parts/pbox-form', array(
			'title' => '%title',
			'content' => '%content'
	)) ?>
</div>


</body>
</html>