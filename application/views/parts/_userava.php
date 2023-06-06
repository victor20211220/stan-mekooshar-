<?// dump($user, 1) // TODO?>
<?// dump($avasize, 1) ?>
<?// dump($isTooltip, 1) ?>
<?// dump($text, 1) ?><?
 if(!isset($isTooltip)) {
	 $isTooltip = TRUE;
 }
$user_id = (isset($user->userId) ? $user->userId : $user->id);



 $company = (!empty($user->companyName)) ? $user->companyName : $user->universityName;
 $headline = $user->userHeadline;
 $username = $user->userFirstName . ' ' . $user->userLastName;

$size = 'userava_44';
$nosize = 'noimage_44.jpg';
switch($avasize){
	case 'avasize_44':
		$size = 'userava_44';
		$nosize = 'noimage_44.jpg';
		break;
	case 'avasize_50':
		$size = 'userava_50';
		$nosize = 'noimage_50.jpg';
		break;
	case 'avasize_52':
		$size = 'userava_52';
		$nosize = 'noimage_52.jpg';
		break;
	case 'avasize_94':
		$size = 'userava_94';
		$nosize = 'noimage_94.jpg';
		break;
	case 'avasize_174':
		$size = 'userava_174';
		$nosize = 'noimage_174.jpg';
		break;
}

if(is_null($user->avaToken)) {
	$profileAva = '/resources/images/'. $nosize;
} else {
	$profileAva = Model_Files::generateUrl($user->avaToken, 'jpg', FILE_USER_AVA, TRUE, false, $size);
}

?><a href="<?= Request::generateUri('profile', $user_id) ?>" class="userava <?= isset($avasize) ? $avasize : 'avasize_44' ?> <?= ($isTooltip === TRUE) ? 'is-tooltip' : '' ?>" title="View profile" data-id="<?= $user_id ?>">
	<img src="<?= $profileAva ?>" title="" /><div class="userava-info">
		<div>
			<div class="userava-name"><?= $username ?></div>
			<? if(isset($isTooltip) && $isTooltip == true && !empty($headline) && !empty($company)) : ?>
				<div class="userava-headline_and_company"><?= $headline ?> | <?= $company ?></div>
			<? else: ?>
				<div class="userava-headline"><?= $headline ?></div>
				<div class="userava-company"><?= $company ?></div>
			<? endif; ?>
			<? if(isset($text)) : ?>
				<div class="userava-text">
					<?= $text ?>
				</div>
			<? endif; ?>
		</div>
	</div>
</a>