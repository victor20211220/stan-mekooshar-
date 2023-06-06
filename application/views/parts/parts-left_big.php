<?// dump($left_class, 1); ?>

<div class="parts-left_big">
	<? if((isset($lefttop) && $lefttop) || (isset($left) && $left)) : ?>
		<div class="parts-left_big-leftouter">
			<? if(isset($lefttop) && $lefttop) : ?>
				<div class="parts-left_big-lefttop">
					<?= $lefttop ?>
				</div>
			<? endif; ?>
			<? if(isset($leftmiddle) && $leftmiddle) : ?>
				<div class="parts-left_big-leftmiddle">
					<?= (isset($leftmiddle)) ? $leftmiddle : null ?>
				</div>
			<? endif; ?>
			<? if(isset($left) && $left) : ?>
				<div class="parts-left_big-left">
					<?= (isset($left)) ? $left : null ?>
				</div>
			<? endif; ?>
		</div><? endif; ?><? if(isset($right) && $right) : ?><div class="parts-left_big-right">
			<?= (isset($right)) ? $right : null ?>
		</div>
	<? endif; ?>
</div>
