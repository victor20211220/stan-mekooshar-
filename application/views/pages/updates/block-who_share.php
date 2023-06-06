<?// dump($showShare, 1); ?>
<?// dump($showLike, 1); ?>
<?// dump($timeline); ?>
<div class="update-who_shares <?= ((!($showLike && isset($timeline->likesPeople)) && ($showShare && isset($timeline->sharesPeople))) ? 'active' : null) ?>" style="<?= (!($showLike && isset($timeline->likesPeople)) && ($showShare && isset($timeline->sharesPeople))) ? 'display:block;' : null ?>" onmouseover="web.showShares(this);">
	<? if($showShare && isset($timeline->sharesPeople)) : ?>
			<?
			$i = 0;
			$shares = explode(',', $timeline->sharesPeople);

			if(in_array($timeline->type, array(TIMELINE_TYPE_COMMENTS, TIMELINE_TYPE_LIKE))) {
				$countShares = $timeline->parentCountShare;
			} else {
				$countShares = $timeline->countShare;
			}

			foreach($shares as $share) :
				$i ++;
				$tmp = explode('_', $share);
				?>
				<a href="<?= Request::generateUri('profile', $tmp[0]) ?>" title="View profile"><?= $tmp[1] ?></a>
				<?

				if($i < 3 && $countShares != $i) :
					if(($i == 2 && $countShares == 3) || ($i == 1 && $countShares == 2)) :
						echo ' and ';
					else:
						echo ', ';
					endif;
				endif;
			endforeach;

			if($countShares > 3) : ?>
				and <a href="<?= Request::generateUri('updates', 'showShareList', $timeline->id) ?>" onclick="return box.load(this, 'sharesPeople');" title="Show list"><?= $countShares - $i ?> other</a>
			<? endif; ?> shared this

		<script type="text/javascript">
			$('li[data-id="timeline_<?= $timeline->id ?>"] .update-who_likes').removeClass('hidden');
		</script>
	<? endif ?>
</div>