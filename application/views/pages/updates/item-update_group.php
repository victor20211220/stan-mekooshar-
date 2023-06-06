<?
$countFollow = (is_null($timeline->postCountGroupFollow)) ? 0 : $timeline->postCountGroupFollow;

if(!$isAllowedProfile) {
	$profileAva = '/resources/images/' . $invisiblesize;
}

$levelConnection = FALSE;
$levelConnectionText = '';
if(in_array($timeline->type, array(TIMELINE_TYPE_COMMENTSDUSCUSSION, TIMELINE_TYPE_LIKEDUSCUSSION))) {
	$levelConnection = Model_User::getLevelWithUser($user_id);
	switch($levelConnection){
		case 1:
			$levelConnectionText = '1st';
			break;
		case 2:
			$levelConnectionText = '2nd';
			break;
		case 3:
			$levelConnectionText = '3rd';
			break;
		case 4:
			$levelConnectionText = '';
			break;
	}
}

?>

<? if(!$isSub) : ?>
<li data-id="timeline_<?= $timeline->id ?>">
	<div>
<? endif; ?>

		<div class="update-info  <?= ($isSub) ? 'update-is_sub' : null ?>">
			<div>

				<? if(($timeline->groupMemberType == GROUP_MEMBER_TYPE_ADMIN || $timeline->ownerId == $user->id || $isMyUpdate) && $isEditPanels) : ?>
					<? switch($timeline->type) :
						case TIMELINE_TYPE_COMMENTSDUSCUSSION:
						case TIMELINE_TYPE_LIKEDUSCUSSION: ?>
							<? break;
						default: ?>
							<div class="update-editpanel">
								<a href="<?= Request::generateUri('groups', 'deleteDiscussion', $timeline->id); ?>" onclick="return box.confirm(this, true);" class="icons i-close icon-text btn-icon" title="Delete update"><span></span></a>
								<? if($isEdit) : ?>
									<a href="<?= Request::generateUri('groups', 'editDiscussion', $timeline->id); ?>" onclick="web.blockProfileEdit();  return web.ajaxGet(this);" class="btn-roundblue-border icons i-editcustom" title="Edit discussion"><span></span>Edit</a>
								<? endif; ?>
							</div>
						<? endswitch; ?>
				<? endif; ?>
					<div class="update-user_name">
						<? if($isUsernameLink) : ?>
							<b><a href="<?= $profileUrl ?>" title="<?= $profileUrlTitle ?>"><?= $userName ?></a></b>
						<? else: ?>
							<b><?= $userName ?></b>
						<? endif ?>

						<? if($showTimelineType) : ?>
							<span class="update-type_label icons update-type-<?= $timeline->type ?>"> <span></span><?= t('updates_type_label.' . $timeline->type) ?></span>
						<? endif ?>
					</div>

					<? if($isUsernameLink) : ?>
						<a class="update-img_updater" href="<?= $profileUrl ?>" title="<?= $profileUrlTitle ?>">
							<? if($levelConnection) : ?>
								<div class="userava-level_connection"><?= $levelConnectionText ?></div>
							<? endif; ?>
							<img src="<?= $profileAva ?>" title="" width="106"/>
						</a><div class="update-textblock">
					<? else : ?>
						<div>
							<? if($levelConnection) : ?>
								<div class="userava-level_connection"><?= $levelConnectionText ?></div>
							<? endif; ?>
							<img src="<?= $profileAva ?>" title="" width="106"/>
						</div><div class="update-textblock">
					<? endif ?>

					<div class="update-text">
						<? switch($timeline->type) :
							case TIMELINE_TYPE_POST: ?>
								<? if($isDiscussionTitleLink) : ?>
									<a class="update-titletext" href="<?= Request::generateUri('groups', 'discussion', $timeline->id) ?>"><?= Html::chars($timeline->postTitle) ?></a>
								<? else : ?>
									<div class="update-titletext"><?= Html::chars($timeline->postTitle) ?></div>
								<? endif; ?>

								<? switch($timeline->postType) :
									case POST_TYPE_TEXT: ?>
										<div class="update-text-post_text">
											<?= nl2br(Html::chars($timeline->postText)) ?>
										</div>

										<? break;
									case POST_TYPE_IMAGE: ?>
										<? $preview_url = Model_Files::generateUrl($timeline->postAlias, 'jpg', FILE_UPDATES, TRUE, false, 'preview'); ?>
										<? list($imgWidth, $imgHeight, $imgType, $imgAttr) = getimagesize(realpath(NULL) . $preview_url); ?>
										<div class="update-text-post_image">
											<?= nl2br(Html::chars($timeline->postText)) ?>
											<img src="<?= Model_Files::generateUrl($timeline->postAlias, 'jpg', FILE_UPDATES, TRUE, false, $post_image_size) ?>" onclick="box.previewImage(this);" data-imgwidth="<?= $imgWidth ?>" data-imgheight="<?= $imgHeight ?>" data-img="<?= $preview_url ?>" />
										</div>

										<? break;
									case POST_TYPE_DOC:
									case POST_TYPE_PDF:
									case POST_TYPE_WEB: ?>
										<div class="update-text-post_web">
											<?= nl2br(Html::chars($timeline->postText)) ?>
											<? if(!is_null($timeline->postAlias)) : ?>
												<? $preview_url = Model_Files::generateUrl($timeline->postAlias, 'jpg', FILE_UPDATES, TRUE, false, 'preview'); ?>
												<? list($imgWidth, $imgHeight, $imgType, $imgAttr) = getimagesize(realpath(NULL) . $preview_url); ?>
												<img src="<?= Model_Files::generateUrl($timeline->postAlias, 'jpg', FILE_UPDATES, TRUE, false, $post_image_size) ?>" onclick="box.previewImage(this);" data-imgwidth="<?= $imgWidth ?>" data-imgheight="<?= $imgHeight ?>" data-img="<?= $preview_url ?>" />
											<? endif; ?>
										</div>

										<? break;
									endswitch ?>
								<? break; ?>
							<? case TIMELINE_TYPE_COMMENTSDUSCUSSION: ?>
							<? case TIMELINE_TYPE_LIKEDUSCUSSION: ?>
								<?
								$parentTimeline = clone $timeline;
								$parentTimeline->id = $timeline->parentId;
								$parentTimeline->user_id = $timeline->parentUserId;
								$parentTimeline->company_id = $timeline->parentCompanyId;
								$parentTimeline->createDate = $timeline->parentCreateDate;
								$parentTimeline->type = $timeline->parentType;
								$parentTimeline->content = $timeline->parentContent;
								$parentTimeline->countLikes = $timeline->parentCountLikes;
								$parentTimeline->countComments = $timeline->parentCountComments;
								$parentTimeline->countShare = $timeline->parentCountShare;
								$parentTimeline->post_id = $timeline->parentPostId;
								$parentTimeline->parent_id = $timeline->parentParentId;

								$parentTimeline->parentId = NULL;
								$parentTimeline->parentUserId = NULL;
								$parentTimeline->parentCompanyId = NULL;
								$parentTimeline->parentCreateDate = NULL;
								$parentTimeline->parentType = NULL;
								$parentTimeline->parentContent = NULL;
								$parentTimeline->parentCountLikes = NULL;
								$parentTimeline->parentCountComments = NULL;
								$parentTimeline->parentCountShare = NULL;
								$parentTimeline->parentPostId = NULL;
								$parentTimeline->parentParentId = NULL;
								?>
								<div>
									<?= nl2br(Html::chars($timeline->content)) ?>
									<div class="update-shared">
										<?= View::factory('pages/updates/item-update', array(
											'avasize' => 'avasize_44',
											'timeline' => $parentTimeline,
											'isEditPanels' => FALSE,
											'isPanelsSocial' => FALSE,
											'isUsernameLink' => TRUE,
											'isSub' => TRUE,
											'textLen' => 200,
											'showTimelineType' => FALSE
										));
										?>
									</div>
								</div>
								<? break; ?>
						<? endswitch; ?>
					</div>

					<? if($isPanelsSocial) : ?>
						<? switch($timeline->type) :
							case TIMELINE_TYPE_COMMENTSDUSCUSSION:
							case TIMELINE_TYPE_LIKEDUSCUSSION: ?>
								<div class="list-panel-btns">
									<? if($showLike) : ?>
										<a href="<?= Request::generateUri('updates', 'like', $timeline->id); ?>"  onclick="return web.ajaxGet(this);" onmouseover="web.showLikes(this);" class="icons i-like icon-text btn-icon <?= (isset($timeline->timelineLikesUserId) && !is_null($timeline->timelineLikesUserId)) ? 'active' : null ?>" > <span></span><div data-visiblezero="true"><?= $timeline->parentCountLikes ?></div></a>
									<? endif ?>

									<? if($showComment) : ?>
										<a href="<?= Request::generateUri('updates', 'comments', $timeline->id); ?>"  onclick="return web.showHideComments(this)" class="icons i-comments icon-text btn-icon" > <span></span><div data-visiblezero="true"><?= $timeline->parentCountComments ?></div> </a>
									<? endif ?>

									<? if($showFollow) : ?>
										<a href="<?= Request::generateUri('groups', 'followDiscussion', $timeline->parentId); ?>"  onclick="return web.ajaxGet(this);" onmouseover="web.showFollowDiscussion(this);"  class="icons i-followdiscussion icon-text btn-icon follow-discussion" data-count="<?= $countFollow ?>" > <span></span><div data-visiblezero="true"><?= $countFollow ?></div></a>
									<? endif ?>
								</div>
								<div class="list-panel-bottom">
									<?= date('m.d.Y h:i A', strtotime($timeline->parentCreateDate)) ?>
								</div>

								<? break;
							default : ?>
								<div class="list-panel-btns">
									<? if($showLike) : ?>
										<a href="<?= Request::generateUri('updates', 'like', $timeline->id); ?>"  onclick="return web.ajaxGet(this);" onmouseover="web.showLikes(this);"  class="icons i-like icon-text btn-icon <?= (isset($timeline->timelineLikesUserId) && !is_null($timeline->timelineLikesUserId	)) ? 'active' : null ?>"  title="Like discussion" > <span></span><div data-visiblezero="true"><?= $timeline->countLikes ?></div></a>
									<? endif ?>

									<? if($showComment) : ?>
										<a href="<?= Request::generateUri('updates', 'comments', $timeline->id); ?>"  onclick="return web.showHideComments(this)" class="icons i-comments icon-text btn-icon" title="Comments discussion" > <span></span><div data-visiblezero="true"><?= $timeline->countComments ?></div> </a>
									<? endif ?>

									<? if($showFollow) : ?>
										<a href="<?= Request::generateUri('groups', 'followDiscussion', $timeline->id); ?>"  onclick="return web.ajaxGet(this);"  onmouseover="web.showFollowDiscussion(this);" class="icons i-followdiscussion icon-text btn-icon follow-discussion <?= (!is_null($timeline->groupDiscussionFollowUserId)) ? 'active' : null ?>" data-count="<?= $countFollow ?>"  title="Follow discussion" > <span></span><div data-visiblezero="true"><?= $countFollow ?></div></a>
									<? endif ?>

									<? if($showReadMore) : ?>
										<a href="<?= Request::generateUri('groups', 'discussion', $timeline->id); ?>"  class="icon-next icon-text btn-icon readmore-discussion" title="Read more discussion" > <span></span><div>read more</div></a>
									<? endif ?>

									<? if($showAcceptContent) : ?>
										<a href="<?= Request::generateUri('groups', 'acceptDiscussions', array($timeline->postGroupId ,$timeline->id, TRUE)); ?>"   onclick="return web.ajaxGet(this);"  class="icons i-accept icon-text btn-icon"  title="Accept discussion" > <span></span><div>accept</div></a>
									<? endif ?>

									<? if($showDeleteContent) : ?>
										<a href="<?= Request::generateUri('groups', 'deleteDiscussions', array($timeline->postGroupId ,$timeline->id, TRUE)); ?>"  onclick="return web.ajaxGet(this);"   class="icons i-close icon-text btn-icon" title="Delete discussion" > <span></span><div>delete</div></a>
									<? endif ?>
								</div>
								<div class="list-panel-bottom">
									<?= date('m.d.Y h:i A', strtotime($timeline->createDate)) ?>
								</div>
						<? endswitch; ?>

							<?= View::factory('pages/updates/block-who_like', array(
								'timeline' => $timeline,
								'showLike' => $showLike,
								'showShare' => $showShare,
								'showFollowDiscussion' => $showFollowDiscussion
							)); ?>
						<?= View::factory('pages/updates/block-who_follow_discussion', array(
							'timeline' => $timeline,
							'showLike' => $showLike,
							'showFollowDiscussion' => $showFollowDiscussion
						)); ?>

					<? endif; ?>
					<? if(!$isSub) : ?>
						<div class="update-comments"></div>
					<? endif; ?>
				</div>
			</div>
		</div>

<? if(!$isSub) : ?>
	</div>
	<? if($isCheck) : ?>
		<div class="checkbox-control-select" data-id="<?= $timeline->id ?>" ></div>
	<? endif ?>
</li>
<? endif; ?>
<?
unset($parentTimeline);
?>