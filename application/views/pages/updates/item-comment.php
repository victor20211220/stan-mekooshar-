<?// dump($comment); ?>
<?// dump($parentTimeline_id, 1); ?>

<li data-id="comment_<?= $comment->id ?>">
	<?= View::factory('parts/userava-more', array(
		'isLinkProfile' => FALSE,
		'isComments' => TRUE,
		'isUsernameLink' => TRUE,
		'comment' => $comment,
		'keyId' => 'user_id'
	)) ?>
</li>

<? if(isset($isAddComment)) : ?>
<script type="text/javascript">
	web.addCountTo('li[data-id="<?= $comment->timeline_id ?>"] .i-comments div');
	$('#addupdatecomments_<?= $comment->timeline_id ?>-text').val('');

	<? if(isset($lastTimeline_id)) : ?>
		web.addCountTo('li[data-id="<?= $lastTimeline_id ?>"] .i-comments div');
		$('#addupdatecomments_<?= $lastTimeline_id ?>-text').val('');
	<? endif; ?>
</script>
<? endif; ?>