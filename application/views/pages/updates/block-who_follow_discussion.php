<?// dump($showFollowDiscussion, 1); ?>
<?// dump($showLike, 1); ?>
<?// dump($timeline, 1); ?>

<div class="update-who_follow_discussion <?= (!($showLike && isset($timeline->likesPeople)) && ($showFollowDiscussion && isset($timeline->followDiscussion))) ? 'active' : null ?>" style="<?= (!($showLike && isset($timeline->likesPeople)) && ($showFollowDiscussion && isset($timeline->followDiscussion))) ? 'display:block;' : null ?>" onmouseover="web.showFollowDiscussion(this);">
	<? if($showFollowDiscussion && isset($timeline->followDiscussion)) : ?>
			<?
			$i = 0;
			$followers = explode(',', $timeline->followDiscussion);
			$countFollows = $timeline->postCountGroupFollow;

			foreach($followers as $follow) :
				$i ++;
				$tmp = explode('_', $follow);
				?>
				<a href="<?= Request::generateUri('profile', $tmp[0]) ?>" title="View profile"><?= $tmp[1] ?></a>
				<?

				if($i < 3 && $countFollows != $i) :
					if(($i == 2 && $countFollows == 3) || ($i == 1 && $countFollows == 2)) :
						echo ' and ';
					else:
						echo ', ';
					endif;
				endif;
			endforeach;

			if($countFollows > 3) : ?>
				and <a href="<?= Request::generateUri('updates', 'showFollowDiscussionList', $timeline->id) ?>" onclick="return box.load(this, 'sharesPeople');" title="Show list"><?= $countFollows - $i ?> other</a>
			<? endif; ?> shared this

		<script type="text/javascript">
			$('li[data-id="timeline_<?= $timeline->id ?>"] .update-who_likes').removeClass('hidden');
		</script>
	<? endif ?>
</div>