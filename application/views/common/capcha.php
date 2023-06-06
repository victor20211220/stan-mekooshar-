<div class="captcha-outer">
	<div class="capcha captchaInit" id="capcha-<?=$active ?>">
		<? foreach($heroes as $hero) : ?>
		<div class="hero" id="hero-<?=$hero ?>">
			<img src="/captcha/load/<?=$hero ?>/" data-value="<?=$hero ?>" onclick="captcha.select(this);" />
		</div>
		<? endforeach; ?>
	</div>
	<div class="capcha-select">
		select <?=$select ?>
	</div>
</div>

<script type="text/javascript">
captcha = {
	select : function(target) {
		$(target).closest('.capcha').find('img').css('opacity', 1).filter(target).css('opacity', 0.5);
		$(target).closest('form').find('input[for=captcha]').val($(target).data('value'));
	}
};
</script>