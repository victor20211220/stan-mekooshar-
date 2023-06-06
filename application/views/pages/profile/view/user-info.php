<?// dump($userinfo_experience, 1); ?>
<?// dump($userinfo_education, 1); ?>
<?// dump($isConnections, 1); ?>
<?// dump($profile, 1); ?>
<?// dump($items_connections, 1); ?>

<? $countConnections = $items_connections['paginator']['count'] ?>
<?

	$headline = array();
	$contries = t('countries');
	$industries = t('industries');

	if($isConnections) {
		$isConnected = false;
		$isInvited = false;
		foreach($isConnections['data'] as $isConnection){
			if($isConnection->typeApproved == 1) {
				$isConnected = true;
				break;
			}
			if($isConnection->typeApproved == 0) {
				$isInvited = true;
				break;
			}
		}
	}
	$isRequest = Model_Connections::isSendRequestByConnection($profile->id, $user->id);

	$isAllowedProfile = false;
	if($profile->setInvisibleProfile == 0 || $user->id == $profile->id ||
		($profile->setInvisibleProfile == USER_PROFILE_INVISIBLE && $isConnected && $user->id != $profile->id) ||
		($profile->setInvisibleProfile == USER_PROFILE_INVISIBLE && $isRequest && $user->id != $profile->id))
	{
		$isAllowedProfile = true;
	}


	if(!empty($profile->professionalHeadline)) {
		$headline[] = $profile->professionalHeadline;
	}
	if(!empty($profile->country) && isset($contries[$profile->country])) {
		$headline[] = $contries[$profile->country];
	}
	if(!empty($profile->industry) && isset($industries[$profile->industry])) {
		$headline[] = $industries[$profile->industry];
	}
	$headline = implode(' | ', $headline);


	if(!isset($isCountConnections)) {
		$isCountConnections = TRUE;
	}
	if(!isset($isBtnContactInfo)) {
		$isBtnContactInfo = TRUE;
	}


	$current = array();
	$previous = array();
	if(!empty($userinfo_experience['data'])) {
		foreach($userinfo_experience['data'] as $item) {
			if($item->isCurrent == 1) {
				$current = $item;
				continue;
			} else {
				if(empty($previous)) {
					$previous = $item;
				}
			}
		}
	}

	$education = array();
	if(!empty($userinfo_education['data'])) {
		foreach($userinfo_education['data'] as $item) {
			$education = $item;
			break;
		}
	}

	$websites = array();
	if(!empty($profile->websites)) {
		$websites = unserialize($profile->websites);
	}

	// Check is user block you
	$isUserBlockMe = FALSE;
	if($profile->id != $user->id) {
		$isUserBlockMe = Model_User::checkIsUserBlockMe($profile->id);
	}



	if(!$isAllowedProfile || $isUserBlockMe) {
		$profileAva = '/resources/images/blockedimage_174.jpg';
	} else {
		if(is_null($profile->avaToken)) {
			$profileAva = '/resources/images/noimage_174.jpg';
		} else {
			$profileAva = Model_Files::generateUrl($profile->avaToken, 'jpg', FILE_USER_AVA, TRUE, false, 'userava_174');
		}
	}

	$isShowConnections = Model_Connections::checkAllowMeToProffileConnections($profile, USER_LEVEL_ACCESS_SHOW_CONNECTIONS);
	$isShowContactInfo = Model_Connections::checkAllowMeToProffileConnections($profile, USER_LEVEL_ACCESS_SHOW_CONTACTINFO);

	$levelConnection = Model_User::checkCanSendToUser($profile->id);
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


<div class="block-userinfo <?= (!$isCountConnections) ? 'noCountConnections' : null?>">
	<div class="userinfo-left">
		<div class="userinfo-photo">
			<? if($levelConnection) : ?>
				<div class="userava-level_connection"><?= $levelConnectionText ?></div>
			<? endif; ?>
			<img src="<?= $profileAva; ?>" alt="" title="" />
		</div>


		<? if($isAllowedProfile && ($isShowContactInfo || $profile->id == $user->id)) : ?>
			<? if($isBtnContactInfo) : ?>
				<? if(!empty($profile->email2) || $profile->phone || !empty($profile->fullAddress) || !empty($websites)) : ?>
					<?	if( ! in_array($levelConnection, array(1, 2, 3)) && $user->accountType != ACCOUNT_TYPE_GOLD && $profile->id != $user->id) : ?>
						<a href="<?= Request::generateUri('profile', 'upgrade'); ?>" class="btn-blue bi-twodot" title="Show/hide contact information">Contact information</a>
					<? else : ?>
						<a href="#" class="btn-blue bi-twodot" onclick="return web.showHideContactInfo(this);" title="Show/hide contact information">Contact information</a>
					<? endif; ?>
				<? endif; ?>
			<? endif; ?>
		<? endif; ?>

		<? if($profile->id == $user->id) : ?>
			<a href="<?= Request::generateUri('profile', 'edit')?>" class="userinfo-editprofile btn-roundblue-border icons i-editcustom " title=""><span></span>Edit profile</a>
		<? endif; ?>

		<? if(!$isUserBlockMe) : ?>
			<? if($profile->id != $user->id) : ?>
                <? if( ! in_array($levelConnection, array(1, 2, 3))  && $user->accountType != ACCOUNT_TYPE_GOLD && $profile->id != $user->id) : ?>
						<a href="<?= Request::generateUri('profile', 'upgrade'); ?>" class="btn-blue userinfo-addconnection icons i-messageswhite"  title="Send message"><span></span>Send message</a>
					<? else : ?>
						<a href="<?= Request::generateUri('messages', 'sentMessageFromProfile', $profile->id) ?>" onclick="return box.load(this);" class="btn-blue userinfo-addconnection icons i-messageswhite"  title="Send message"><span></span>Send message</a>
					<? endif; ?>

				<? if(!$isConnected && $isInvited): ?>
					<div class="btn-blue is-blocked userinfo-invitationsent">Invitation sent</div>
				<? endif ?>
				<? if(!$isConnected && !$isInvited): ?>
					<a href="<?= Request::generateUri('connections', 'addConnections', $profile->id) ?>" onclick="return box.load(this);" class="btn-blue userinfo-addconnection icons i-add"  title="Add connection"><span></span>Add connection</a>
				<? endif; ?>
			<? endif ?>
		<? endif ?>

	</div><div class="userinfo-right">
<!--		--><?// if($profile->id == $user->id) : ?>
<!--			<a href="--><?//= Request::generateUri('profile', 'edit')?><!--" class="userinfo-editprofile btn-roundblue-border icons i-editcustom " title=""><span></span>Edit profile</a>-->
<!--		--><?// endif; ?>
		<? if(isset($isNameLink) && $isNameLink) : ?>
			<div class="userinfo-name"><a href="<?= Request::generateUri('profile', ((!empty($profile->alias)) ? $profile->alias : $profile->id)); ?>"><?= $profile->firstName . ' ' . $profile->lastName ?></a></div>
		<? else: ?>
			<div class="userinfo-name"><?= $profile->firstName . ' ' . $profile->lastName ?></div>
		<? endif; ?>
		<? if(!$isUserBlockMe) : ?>

				<? if($isAllowedProfile) : ?>
					<? if(!empty($headline)) : ?>
						<div class="userinfo-headline bg-blue"><?= nl2br(HTML::chars($headline)) ?></div>
					<? endif; ?>

					<? if(in_array($levelConnection, array(1, 2, 3)) || $user->accountType == ACCOUNT_TYPE_GOLD || $profile->id == $user->id) : ?>
						<div class="userinfo-otherinfo">
							<? if(!empty($current)) : ?>
								<span class="text-title">Company: </span><?= (!empty($current->companyName)) ? HTML::chars($current->companyName) : HTML::chars($current->universityName) ; ?><br>
							<? endif; ?>
							<? if(!empty($previous)) : ?>
								<span class="text-title">Previous: </span><?= (!empty($previous->companyName)) ? HTML::chars($previous->companyName) : HTML::chars($previous->universityName) ; ?><br>
							<? endif; ?>
							<? if(!empty($education)) : ?>
								<span class="text-title">Education: </span><?= HTML::chars($education->universityName) ?>
							<? endif; ?>
						</div>
					<? endif; ?>
				<? else: ?>
					<div class="list-item-empty">
						Info is private
					</div>
				<? endif; ?>
		<? else: ?>
			<div class="list-item-empty">
				You can not view this profile. This user block you.
			</div>
		<? endif; ?>
	</div>
	<? if(!$isUserBlockMe) : ?>
		<? if(in_array($levelConnection, array(1, 2, 3)) || $user->accountType == ACCOUNT_TYPE_GOLD || $profile->id == $user->id) : ?>
			<? if($isAllowedProfile) : ?>
				<? if($isShowContactInfo || $profile->id == $user->id) : ?>
					<? if(!empty($profile->email2) || !empty($profile->phone) || !empty($profile->fullAddress) || !empty($websites) || !empty($profile->alias)) : ?>
						<div class="userinfo-contactinfo">
							<? if(!empty($profile->alias)) : ?>
								<? $uri = Request::$protocol . '://' . Request::$host . '/' . Html::chars($profile->alias); ?>
								<span class="text-title">Public profile url: </span><a class="userinfo-public_link" href="<?= Request::generateUri(Html::chars($profile->alias), 'index') ?>" target="_blank"><?= $uri ?></a><br>
							<? endif; ?>
							<? if(!empty($profile->email2)) : ?>
								<span class="text-title">Email: </span><?= Html::chars($profile->email2) ?><br>
							<? endif; ?>
							<? if(!empty($profile->phone)) : ?>
								<span class="text-title">Phone: </span><?= Html::chars($profile->phone) ?><br>
							<? endif; ?>
							<? if(!empty($profile->fullAddress)) : ?>
								<span class="text-title">Address: </span><?= Html::chars($profile->fullAddress) ?><br>
							<? endif; ?>
							<? if(!empty($websites)) : ?>
								<span class="text-title">Websites: </span><br>
								<? foreach($websites as $website) : ?>
									<a class="icons i-link icon-round-min icon-text" href="<?= Html::chars($website) ?>" title="" target="_blank"><span></span><?= Html::chars($website) ?></a>
								<? endforeach; ?>
							<? endif; ?>
						</div>
					<? endif; ?>
				<? endif; ?>
				<? if(isset($countConnections) && $isCountConnections && $isShowConnections) : ?>
					<a class="userinfo-countconnections" href="<?= Request::generateUri('profile', 'connections', $profile->id)?>">Connections <?= $countConnections ?></a>
				<? endif; ?>
			<? endif; ?>
		<? endif; ?>
<!--	--><?// else: ?>
<!--		<div class="list-item-empty">-->
<!--			You can not view this profile. This user block you.-->
<!--		</div>-->
	<? endif; ?>
</div>

<?
if(isset($_COOKIE['addConnectProfile'])) {
	$id = $_COOKIE['addConnectProfile'];
	setcookie('addConnectProfile', null, -1, '/');
	?>
	<script type="text/javascript">
		$('.userinfo-addconnection').click();
	</script>
	<?
}
?>