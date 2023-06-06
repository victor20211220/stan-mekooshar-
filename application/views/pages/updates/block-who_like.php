<?// dump($showShare, 1); ?>
<?// dump($showLike, 1); ?>
<?// dump($timeline, 1); ?>
<?// $tmp = !($showShare && isset($timeline->sharesPeople)); ?>
<?// dump($tmp) ?>
<div class="update-who_likes <?= ($showLike && isset($timeline->likesPeople)) ? 'active' : null ?>  <?= (($showLike && isset($timeline->likesPeople)) || ($showShare && isset($timeline->sharesPeople))) ? null : 'hidden' ?> ">
	<? if($showLike && isset($timeline->likesPeople)) : ?>
		<?
		$i = 0;
		$likes = explode(',', $timeline->likesPeople);

		if(in_array($timeline->type, array(TIMELINE_TYPE_COMMENTS, TIMELINE_TYPE_LIKE))) {
			$countLikes = $timeline->parentCountLikes;
		} else {
			$countLikes = $timeline->countLikes;
		}

		foreach($likes as $like) :
			$i ++;
			$tmp = explode('_', $like);
			?>
			<a href="<?= Request::generateUri('profile', $tmp[0]) ?>" title="View profile"><?= $tmp[1] ?></a>
			<?

			if($i < 3 && $countLikes != $i) :
				if(($i == 2 && $countLikes == 3) || ($i == 1 && $countLikes == 2)) :
					echo ' and ';
				else:
					echo ', ';
				endif;
			endif;
		endforeach;

		if($countLikes > 3) : ?>
			and <a href="<?= Request::generateUri('updates', 'showLikeList', $timeline->id) ?>" onclick="return box.load(this, 'likesPeople');" title="Show list"><?= $countLikes - $i ?> other</a>
		<? endif; ?> liked this


		<script type="text/javascript">
			$('li[data-id="timeline_<?= $timeline->id ?>"] .update-who_shares').removeClass('active');
		</script>
	<? elseif($showLike && isset($timeline->likesPeople)) : ?>
		<script type="text/javascript">
			$('li[data-id="timeline_<?= $timeline->id ?>"] .update-who_shares').css('display', 'block').addClass('active');
		</script>
	<? elseif($showFollowDiscussion && isset($timeline->followDiscussion)) : ?>
		<script type="text/javascript">
			$('li[data-id="timeline_<?= $timeline->id ?>"] .update-who_follow_discussion').css('display', 'block').addClass('active');
		</script>
	<? endif ?>
</div>