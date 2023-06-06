<?
if(!$isAllowedProfile) {
	$profileAva = '/resources/images/' . $invisiblesize;
}

$levelConnection = Model_User::getLevelWithUser($user_id);
$levelConnectionText = '';
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
?>


<? if(!$isSub) : ?>
<li data-id="timeline_<?= $timeline->id ?>">
	<div>
<? endif; ?>

		<div class="update-info  <?= ($isSub) ? 'update-is_sub' : null ?>">
			<div>
				<? if($isMyUpdate && $isEditPanels) : ?>
					<? switch($timeline->type) :
						case TIMELINE_TYPE_UPDATEPHOTO:
							?>
							<div class="update-editpanel">
								<a href="<?= Request::generateUri('updates', 'delete', $timeline->id); ?>" onclick="return box.confirm(this, true);" class="icons i-close icon-text btn-icon" title="Delete update"><span></span></a>
							</div>
							<? break;
						case TIMELINE_TYPE_NEWJOB:
						case TIMELINE_TYPE_NEWCONNECTION:
							break;
						case TIMELINE_TYPE_COMMENTS:
						case TIMELINE_TYPE_LIKE: ?>
							<? break;
						default: ?>
							<div class="update-editpanel">
								<a href="<?= Request::generateUri('updates', 'delete', $timeline->id); ?>" onclick="return box.confirm(this, true);" class="icons i-close icon-text btn-icon" title="Delete update"><span></span></a>
								<? if($isEdit) : ?>
									<a href="<?= Request::generateUri('updates', 'edit', $timeline->id); ?>" onclick="web.blockProfileEdit();  return web.ajaxGet(this);" class="btn-roundblue-border icons i-editcustom" title="Edit update"><span></span> Edit</a>
								<? endif; ?>
							</div>
						<? endswitch; ?>
				<? endif; ?>
				<? if(!$isOnlyTextUpdate) : ?>
					<div class="update-user_name">
						<? if($isUsernameLink) : ?>
							<b><a href="<?= $profileUrl ?>" title="<?= $profileUrlTitle ?>"><?= $userName ?></a></b>
						<? else: ?>
							<b><?= $userName ?></b>
						<? endif ?>
						<? if($showTimelineType) : ?>
							<? $update_type = t('updates_type_label'); ?>
							<span class="update-type_label icons update-type-<?= $timeline->type ?>"> <span></span><?= (isset($update_type[$timeline->type])) ? $update_type[$timeline->type] : null ?></span>
						<? endif; ?>
					</div>
				<? endif ?>
				<? if(!$isOnlyTextUpdate) : ?>
					<? if($isUsernameLink) : ?>
						<a class="update-img_updater" href="<?= $profileUrl ?>" title="<?= $profileUrlTitle ?>">
							<? if($levelConnection) : ?>
								<div class="userava-level_connection"><?= $levelConnectionText ?></div>
							<? endif; ?>
							<img src="<?= $profileAva ?>" title="" width="58px"/>
						</a><div class="update-textblock">
					<? else : ?>
						<div>
							<? if($levelConnection) : ?>
								<div class="userava-level_connection"><?= $levelConnectionText ?></div>
							<? endif; ?>
							<img src="<?= $profileAva ?>" title="" width="58px"/>
						</div><div class="update-textblock">
					<? endif ?>
				<? else : ?>
					<div class="update-textblock">
				<? endif ?>
					<div class="update-text">
						<? switch($timeline->type) :
							case TIMELINE_TYPE_POST: ?>
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
											<a href="<?= $timeline->postLink ?>" target="_blank"><?= Html::chars($timeline->postTitle) ?></a>
											<?= nl2br(Html::chars($timeline->postText)) ?>
											<? if(!is_null($timeline->postAlias)) : ?>
												<? $preview_url = Model_Files::generateUrl($timeline->postAlias, 'jpg', FILE_UPDATES, TRUE, false, 'preview'); ?>
												<? list($imgWidth, $imgHeight, $imgType, $imgAttr) = getimagesize(realpath(NULL) . $preview_url); ?>
												<img src="<?= Model_Files::generateUrl($timeline->postAlias, 'jpg', FILE_UPDATES, TRUE, false, $post_image_size) ?>" onclick="box.previewImage(this);"  data-imgwidth="<?= $imgWidth ?>" data-imgheight="<?= $imgHeight ?>" data-img="<?= $preview_url ?>" />
											<? endif; ?>
										</div>

										<? break;
									endswitch ?>
								<? break; ?>
							<? case TIMELINE_TYPE_SHAREPOST: ?>
							<? case TIMELINE_TYPE_COMMENTS: ?>
							<? case TIMELINE_TYPE_LIKE: ?>
								<div>
									<?= nl2br(Html::chars($timeline->content)) ?>
									<div class="update-shared">

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
										$parentTimeline->userSetInvisibleProfile = $timeline->parentUserSetInvisibleProfile;

										if($timeline->parentType == TIMELINE_TYPE_SHAREPOST) {
											$parentTimeline->type = TIMELINE_TYPE_POST;
										}

										if($timeline->parentType == TIMELINE_TYPE_UPDATEPHOTO) {
											$parentTimeline->type = TIMELINE_TYPE_UPDATEPHOTO;

											$parentTimeline->ownerId = $timeline->parentUserId ;
											$parentTimeline->ownerAvaToken = $timeline->parentUserAvaToken ;
											$parentTimeline->ownerFirstName = $timeline->parentUserFirstName ;
											$parentTimeline->ownerLastName = $timeline->parentUserLastName ;
											$parentTimeline->ownerLastName = $timeline->parentUserAlias;
											$parentTimeline->ownerProfessionalHeadline = $timeline->parentUserProfessionalHeadline ;
											$parentTimeline->ownerSetInvisibleProfile = $timeline->parentUserSetInvisibleProfile;
											$showShare = FALSE;
										}

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
										<?= View::factory('pages/updates/item-update', array(
											'avasize' => 'avasize_44',
											'timeline' => $parentTimeline,
											'isEditPanels' => FALSE,
											'isPanelsSocial' => FALSE,
											'isUsernameLink' => TRUE,
											'isSub' => TRUE,
											'isNotification' => $isNotification,
											'showTimelineType' => FALSE
										));
										?>
									</div>
								</div>
								<? break; ?>
							<? case TIMELINE_TYPE_NEWCONNECTION: ?>
								<?
									$friend = (object) array(
										'id' => $timeline->friendId,
										'avaToken' => $timeline->friendAvaToken,
										'firstName' => $timeline->friendFirstName,
										'lastName' => $timeline->friendLastName,
										'userAlias' => $timeline->friendAlias,
										'userHeadline' => $timeline->friendProfessionalHeadline,
										'setInvisibleProfile' => $timeline->friendSetInvisibleProfile
									);
//																 dump($timeline);
								?>
								<?= View::factory('parts/userava-more', array(
									'ouser' => $friend,
								    'avasize' => 'avasize_52',
								    'isLinkProfile' => FALSE,
								    'isUsernameLink' => TRUE,
								    'isShowName' => TRUE,
								    'isShowHeadline' => TRUE,
								)) ?>
							<? break; ?>
							<? case TIMELINE_TYPE_UPDATEPHOTO: ?>
								<? if($isSub) : ?>
									<img class="update_image" src="<?= Model_Files::generateUrl($timeline->ownerAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, 'userava_174') ?>" onclick="box.previewImage(this);" data-img="<?= Model_Files::generateUrl($timeline->ownerAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, 'preview') ?>" width="174px" />
								<? else : ?>
									<img class="update_image" src="<?= Model_Files::generateUrl($timeline->userAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, 'userava_174') ?>" onclick="box.previewImage(this);" data-img="<?= Model_Files::generateUrl($timeline->userAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, 'preview') ?>"  width="174px"/>
								<? endif; ?>
								<? break; ?>
							<? case TIMELINE_TYPE_NEWJOB: ?>
								<?
								$company = (object) array(
									'id' => $timeline->experienceCompanyId,
									'avaToken' => $timeline->experienceCompanyAvaToken,
									'name' => $timeline->experienceCompanyName,
									'isAgree' => $timeline->experienceCompanyIsAgree,
								);

							    if($timeline->experienceCompanyDateFrom) {
								    if($timeline->experienceCompanyIsCurrent){
									    $workInfo = date('m/d/Y', strtotime($timeline->experienceCompanyDateFrom)) . ' to current';
								    } else {
									    $workInfo = date('m/d/Y', strtotime($timeline->experienceCompanyDateFrom)) . ' to ' .
										    date('m/d/Y', strtotime($timeline->experienceCompanyDateTo)) ;
								    }
							    }
								$workInfo .= '<br>' . $timeline->experienceCompanyTitle;

								?>
								<?= View::factory('parts/companiesava-more', array(
									'company' => $company,
									'avasize' => 'avasize_52',
									'isLinkProfile' => FALSE,
									'isCompanyNameLink' => $company->isAgree,
								    'otherInfo' => $workInfo
								)) ?>
								<? break; ?>
						<? endswitch;	?>
					</div>

					<? if($isPanelsSocial) : ?>
						<? switch($timeline->type) :
							case TIMELINE_TYPE_UPDATEPHOTO: ?>
								<div class="list-panel-btns">
									<? if($showLike) : ?>
										<a href="<?= Request::generateUri('updates', 'like', $timeline->id); ?>"  onclick="return web.ajaxGet(this);" class="icons i-like icon-text btn-icon <?= (isset($timeline->timelineLikesUserId) && !is_null($timeline->timelineLikesUserId	)) ? 'active' : null ?>" > <span></span><div data-visiblezero="true"><?= $timeline->countLikes ?></div></a>
									<? endif ?>

									<? if($showComment) : ?>
										<a href="<?= Request::generateUri('updates', 'comments', $timeline->id); ?>"  onclick="return web.showHideComments(this)" class="icons i-comments icon-text btn-icon" > <span></span><div data-visiblezero="true"><?= $timeline->countComments ?></div> </a>
									<? endif ?>
								</div>
								<div class="list-panel-bottom">
									<?= date('m.d.Y h:i A', strtotime($timeline->createDate)) ?>
								</div>
								<? 	break;
							case TIMELINE_TYPE_NEWJOB:
							case TIMELINE_TYPE_NEWCONNECTION:
								?>
								<div class="list-panel-bottom">
									<?= date('m.d.Y h:i A', strtotime($timeline->createDate)) ?>
								</div>
								<? break;
							case TIMELINE_TYPE_COMMENTS:
							case TIMELINE_TYPE_LIKE: ?>
								<div class="list-panel-btns">
									<? if($showLike) : ?>
										<a href="<?= Request::generateUri('updates', 'like', $timeline->id); ?>"  onclick="return web.ajaxGet(this);" onmouseover="web.showLikes(this);" class="icons i-like icon-text btn-icon <?= (isset($timeline->timelineLikesUserId) && !is_null($timeline->timelineLikesUserId	)) ? 'active' : null ?>" > <span></span><div data-visiblezero="true"><?= $timeline->parentCountLikes ?></div></a>
									<? endif; ?>

									<? if($showShare) : ?>
										<? if(!$isMyUpdate && !$isOwner) : ?>
											<a href="<?= Request::generateUri('updates', 'share', $timeline->id); ?>"  onclick="return box.load(this);" onmouseover="web.showShares(this);"   class="icons i-replay icon-text btn-icon <?= (isset($timeline->timelineShareUserId) && !is_null($timeline->timelineShareUserId)) ? 'active' : null ?>" > <span></span><div data-visiblezero="true"><?= $timeline->parentCountShare ?></div></a>
										<? else : ?>
											<span class="icons i-sharenoactive icon-text btn-icon icon-nohover <?= (isset($timeline->timelineShareUserId) && !is_null($timeline->timelineShareUserId)) ? 'active' : null ?>" onmouseover="web.showShares(this);"   data-visiblezero="true" ><span></span><?= $timeline->parentCountShare ?></span>
										<? endif; ?>
									<? endif; ?>

									<? if($showComment) : ?>
										<a href="<?= Request::generateUri('updates', 'comments', $timeline->id); ?>"  onclick="return web.showHideComments(this)" class="icons i-comments icon-text btn-icon" > <span></span><div data-visiblezero="true"><?= $timeline->parentCountComments ?></div> </a>
									<? endif ?>
								</div>
								<div class="list-panel-bottom">
									<?= date('m.d.Y h:i A', strtotime($timeline->parentCreateDate)) ?>
								</div>

								<? break;
							default : ?>
								<div class="list-panel-btns">
									<? if($showLike) : ?>
										<a href="<?= Request::generateUri('updates', 'like', $timeline->id); ?>"  onclick="return web.ajaxGet(this);"   onmouseover="web.showLikes(this);" class="icons i-like icon-text btn-icon <?= (isset($timeline->timelineLikesUserId) && !is_null($timeline->timelineLikesUserId	)) ? 'active' : null ?>" > <span></span><div data-visiblezero="true"><?= $timeline->countLikes ?></div></a>
									<? endif ?>

									<? if($showShare) : ?>
										<? if(!$isMyUpdate) : ?>
											<a href="<?= Request::generateUri('updates', 'share', $timeline->id); ?>"  onclick="return box.load(this);" onmouseover="web.showShares(this);"  class="icons i-replay icon-text btn-icon <?= (isset($timeline->timelineShareUserId) && !is_null($timeline->timelineShareUserId)) ? 'active' : null ?>" > <span></span><div data-visiblezero="true"><?= $timeline->countShare ?></div></a>
										<? else : ?>
											<span class="icons i-sharenoactive icon-text btn-icon icon-nohover <?= (isset($timeline->timelineShareUserId) && !is_null($timeline->timelineShareUserId)) ? 'active' : null ?>" onmouseover="web.showShares(this);" data-visiblezero="true"  ><span></span><?= $timeline->countShare ?></span>
										<? endif;  ?>
									<? endif; ?>

									<? if($showComment) : ?>
										<a href="<?= Request::generateUri('updates', 'comments', $timeline->id); ?>"  onclick="return web.showHideComments(this)" class="icons i-comments icon-text btn-icon" > <span></span><div data-visiblezero="true"><?= $timeline->countComments ?></div> </a>
									<? endif ?>
								</div>
								<div class="list-panel-bottom">
									<?= date('m.d.Y h:i A', strtotime($timeline->createDate)) ?>
								</div>
							<? break;?>
						<? endswitch; ?>

								<?= View::factory('pages/updates/block-who_like', array(
									'timeline' => $timeline,
									'showLike' => $showLike,
									'showShare' => $showShare,
									'showFollowDiscussion' => $showFollowDiscussion
								)); ?>
								<?= View::factory('pages/updates/block-who_share', array(
									'timeline' => $timeline,
									'showLike' => $showLike,
									'showShare' => $showShare
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