<div class="parts-right_big">
	<? if(isset($left)) : ?>
		<div class="parts-right_big-left">
			<?= (isset($left)) ? $left : '' ?>
		</div>
	<? endif; ?>
	<? if(isset($right)) : ?>
		<div class="parts-right_big-right">
			<?= (isset($right)) ? $right : '' ?>
		</div>
	<? endif; ?>
</div>
