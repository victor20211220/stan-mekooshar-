<div class="eva-content">
	<div class="auth-block">
		<a href="<?=Request::generateUri('login')?>"><span class="btn btn-left"></span>login</a>
		or
		<a href="<?=Request::generateUri('register') ?>">register<span class="btn btn-right"></span></a>
		<?if(!empty ($admin)):?>
			<hr/>
			Rental Application: <a href="<?=Request::generateUri('applications') ?>">Apply online<span class="btn btn-right"></span></a>
		<?endif;?>
	</div>
</div>

<script type="text/javascript">
	evaPosition = 'center';
	evaTitle = 'Hi! I’m Eva, your personal helper.';
	evaMessage = 'Log in to the site. Register if you are first time here.';
</script>
		