<?// dump($discussion, 1); ?>
<?// dump($comments, 1); ?>
<?// dump($f_Updates_AddUpdateComments, 1); ?>

<div class="block-discussion">
	<div class="block-list-updates">
		<ul class="list-items">
			<?= View::factory('pages/updates/item-update', array(
				'timeline' => $discussion,
				'showComment' => FALSE,
				'showTimelineType' => FALSE,
				'isDiscussionTitleLink' => FALSE,
				'showLike' => (($discussion->postIsGroupAccept == 1) ? TRUE : FALSE),
				'showFollow' => (($discussion->postIsGroupAccept == 1) ? TRUE : FALSE),
				'showAcceptContent' => (($discussion->postIsGroupAccept == 1) ? FALSE : TRUE),
				'showDeleteContent' => (($discussion->postIsGroupAccept == 1) ? FALSE : TRUE),
				'isEditPanels' => (($discussion->postIsGroupAccept == 1) ? TRUE : FALSE)
			)) ?>
		</ul>
	</div>
	<div class="discussion-comments">
		<?= View::factory('pages/updates/list-comments', array(
			'controller' => Request::generateUri('groups', 'discussion', $discussion->id),
			'autoScroll' => TRUE,
			'comments' => $comments,
			'f_Updates_AddUpdateComments' => $f_Updates_AddUpdateComments,
			'timeline_id' => $discussion->id
		));?>
	</div>
</div>
