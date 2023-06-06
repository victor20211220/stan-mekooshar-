<?// dump($item, 1); ?>
<?// dump($typeViewMessage, 1); ?>
<?// dump($typeListReceived, 1); ?>
<?// dump($typeListHistory, 1); ?>
<?// dump($typeListSent, 1); ?>
<?// dump($typeListArchive, 1); ?>
<?// dump($typeListTrash, 1); ?>

<?
if(!isset($typeListTrash)) {
	$typeListTrash = false;
}
if(!isset($typeListArchive)) {
	$typeListArchive = false;
}
if(!isset($typeListSent)) {
	$typeListSent = false;
}
if(!isset($typeListHistory)) {
	$typeListHistory = false;
}
if(!isset($typeViewMessage)) {
	$typeViewMessage = false;
}
if(!isset($typeListReceived)) {
	$typeListReceived = false;
}
if(!isset($avasize)) {
	$avasize = 'avasize_94';
}

$withSelect = false;
if($typeListTrash || $typeListArchive || $typeListSent || $typeListReceived) {
	$withSelect = true;
}


$isViewed = true;
if($user->id == $item->friend_id && $item->isFriendView == 0){
	$isViewed = false;
}

$isMyMessage = false;
if($user->id == $item->user_id){
	$isMyMessage = true;
}

if($isMyMessage && !$typeListSent) {
	if($typeListHistory) {
		$userName = Html::chars($item->userFirstName . ' ' . $item->userLastName);
		$firstName = $item->userFirstName;
		$lastName = $item->userLastName;
		$avaToken = $item->userAvaToken;
		$userId = $item->userId;
		$userSetInvisibleProfile = $item->userSetInvisibleProfile;

//		if(is_null($item->userAvaToken)) {
//			$profileAva = '/resources/images/noimage_94.jpg';
//		} else {
//			$profileAva = Model_Files::generateUrl($item->userAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, 'userava_94');
//		}
	} else {
		$userName = Html::chars($item->friendFirstName . ' ' . $item->friendLastName) . ' <span>sent by you</span>';
		$firstName = $item->friendFirstName;
		$lastName = $item->friendLastName . ' <span>sent by you</span>';
		$avaToken = $item->friendAvaToken;
		$userId = $item->friendId;
		$userSetInvisibleProfile = $item->friendSetInvisibleProfile;

//		if(is_null($item->friendAvaToken)) {
//			$profileAva = '/resources/images/noimage_94.jpg';
//		} else {
//			$profileAva = Model_Files::generateUrl($item->friendAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, 'userava_94');
//		}
	}
} else {
	$userName = Html::chars($item->userFirstName . ' ' . $item->userLastName);
	$firstName = $item->userFirstName;
	$lastName = $item->userLastName;
	$avaToken = $item->userAvaToken;
	$userId = $item->userId;
	$userSetInvisibleProfile = $item->userSetInvisibleProfile;

//	if(is_null($item->userAvaToken)) {
//		$profileAva = '/resources/images/noimage_94.jpg';
//	} else {
//		$profileAva = Model_Files::generateUrl($item->userAvaToken, 'jpg', FILE_USER_AVA, TRUE, false, 'userava_94');
//	}
}

?>

<li data-id="<?= $item->id ?>">
	<? if($withSelect) : ?>
		<div class="checkbox-control-select" data-id="<?= $item->id ?>"></div>
	<? endif; ?>
	<div>
<!--		<div class="messages-info --><?//= ($typeListHistory) ? 'mini-50' : null ?><!--">-->
<!--			--><?// if($typeViewMessage) : ?>
<!--				<div>-->
<!--			--><?// else: ?>
<!--				<a class="--><?//= (!$isViewed) ? 'active' : null ?><!-- --><?//= ($withSelect) ? 'with-select' : null  ?><!--" href="--><?//= Request::generateUri('messages', 'message', $item->id)?><!--">-->
<!--			--><?// endif; ?>
<!---->
<!--				<div class="messages-user_name"><b>--><?//= $userName ?><!--</b></div>-->
<!--				<img src="--><?//= $profileAva ?><!--" title="" /><div class="messages-message">-->
<!--					<div class="messages-subject">--><?//= Html::chars($item->subject) ?><!--</div>-->
<!--					--><?// if($typeViewMessage) : ?>
<!--						<div class="messages-text">--><?//= nl2br(Html::chars($item->message)) ?><!--</div>-->
<!--					--><?// else : ?>
<!--						--><?// $messages = explode("\r\n", $item->message); ?>
<!--						--><?// $messages_new = array_slice($messages, 0, 3); ?>
<!--						--><?// $messages_new = implode("\r\n" , $messages_new); ?>
<!--						<div class="messages-text">--><?//= nl2br(Html::chars(substr($messages_new, 0, 200))) ?><!----><?//= (strlen($messages_new) > 200) ? '...' : null ?><!--</div>-->
<!--					--><?// endif; ?>
<!--				</div>-->
<!---->
<!--			--><?// if($typeViewMessage) : ?>
<!--				</div>-->
<!--			--><?// else: ?>
<!--				</a>-->
<!--			--><?// endif; ?>
<!--		</div>-->

		<div class="messages-info <?= ($typeListHistory) ? 'mini-50' : null ?>">
			<?
				$ouser = (object) array(
					'id' => $userId,
					'firstName' => $firstName,
					'lastName' => $lastName,
					'avaToken' => $avaToken,
					'setInvisibleProfile' => $userSetInvisibleProfile
				);
			?>
			<?= View::factory('parts/userava-more', array(
				'avasize' => $avasize,
				'ouser' => $ouser,
				'isShowNameOnTop' => TRUE,
				'isLinkProfile' => FALSE,
				'isUsernameLink' => TRUE,
				'isTooltip' => FALSE,
				'hideUseravaInfo' => TRUE
			)) ?>


			<? if($typeViewMessage) : ?>
				<div>
			<? else: ?>
				<a class="<?= (!$isViewed) ? 'active' : null ?> <?= ($withSelect) ? 'with-select' : null  ?>" href="<?= Request::generateUri('messages', 'message', $item->id)?>">
			<? endif; ?>

				<div class="messages-message">
					<div class="messages-subject"><?= Html::chars($item->subject) ?></div>
					<? if($typeViewMessage) : ?>
						<div class="messages-text"><?= nl2br(Html::chars($item->message)) ?></div>
					<? else : ?>
						<? $messages = explode("\r\n", $item->message); ?>
						<? $messages_new = array_slice($messages, 0, 3); ?>
						<? $messages_new = implode("\r\n" , $messages_new); ?>
						<div class="messages-text"><?= nl2br(Html::chars(substr($messages_new, 0, 200))) ?><?= (strlen($messages_new) > 200) ? '...' : null ?></div>
					<? endif; ?>
				</div>

			<? if($typeViewMessage) : ?>
				</div>
			<? else: ?>
				</a>
			<? endif; ?>
		</div>

		<div class="list-panel-btns">
			<? if($typeViewMessage) : ?>
				<a href="<?= Request::generateUri('messages', 'history', $item->userId); ?>"  class="icons i-history icon-text btn-icon <?= (Request::get('page', false)) ? 'hidden' : null ?>" onclick="$(this).addClass('hidden'); $('.messages-history').removeClass('hidden'); return false;"><span></span>History</a>
			<? endif; ?>

<!--			--><?// if($typeListReceived || $typeViewMessage || ($typeListHistory && !$isMyMessage) || ($typeListArchive && !$isMyMessage)) : ?>
			<? if($typeListReceived || ($typeViewMessage && !$isMyMessage) || ($typeListHistory && !$isMyMessage) || ($typeListArchive && !$isMyMessage)) : ?>
				<a href="<?= Request::generateUri('messages', 'reply', $item->id); ?>"  class="icons i-replay icon-text btn-icon" >Reply<span></span></a>
			<? endif; ?>

			<? if($typeListReceived) : ?>
				<a href="<?= Request::generateUri('messages', 'archiveReceived', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-archive icon-text btn-icon" ><span></span>archive</a>
				<a href="<?= Request::generateUri('messages', 'trashReceived', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon" ><span></span>trash</a>
			<? endif; ?>

			<? if($typeListHistory) : ?>
				<? if(($isMyMessage && $item->typeForUser == MESSAGE_ARCHIVE) || (!$isMyMessage && $item->typeForFriend == MESSAGE_ARCHIVE)) : ?>
					<a href="<?= Request::generateUri('messages', 'restoreHistory', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-restore icon-text btn-icon" ><span></span>restore</a>
				<? else: ?>
					<a href="<?= Request::generateUri('messages', 'archiveHistory', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-archive icon-text btn-icon" ><span></span>archive</a>
				<? endif; ?>
				<a href="<?= Request::generateUri('messages', 'trashHistory', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon" ><span></span>trash</a>
			<? endif; ?>

			<? if($typeListSent) : ?>
				<? if($item->typeForUser == MESSAGE_ARCHIVE) : ?>
					<a href="<?= Request::generateUri('messages', 'restoreSent', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-restore icon-text btn-icon" ><span></span>restore</a>
				<? else: ?>
					<a href="<?= Request::generateUri('messages', 'archiveSent', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-archive icon-text btn-icon" ><span></span>archive</a>
				<? endif; ?>
				<a href="<?= Request::generateUri('messages', 'trashSent', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon" ><span></span>trash</a>
			<? endif; ?>

			<? if($typeListArchive) : ?>
				<a href="<?= Request::generateUri('messages', 'restoreArchive', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-restore icon-text btn-icon" ><span></span>restore</a>
				<a href="<?= Request::generateUri('messages', 'trashArchive', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon" ><span></span>trash</a>
			<? endif; ?>

			<? if($typeListTrash) : ?>
				<a href="<?= Request::generateUri('messages', 'restoreTrash', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-restore icon-text btn-icon" ><span></span>restore</a>
				<a href="<?= Request::generateUri('messages', 'delete', $item->id); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon" ><span></span>delete</a>
			<? endif; ?>
		</div>
		<div class="list-panel-bottom">
			<?= date('m.d.Y h:i A', strtotime($item->createDate)) ?>
		</div>
	</div>
</li>