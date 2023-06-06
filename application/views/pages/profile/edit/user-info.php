<?// dump($userinfo_experience, 1); ?>
<?// dump($userinfo_education, 1); ?>
<?// dump($items_connections, 1); ?>
<?// dump($profile, 1); ?>


<? $countConnections = count($items_connections['data']) ?>
<?
	$headline = array();
	$contries = t('countries');
	$industries = t('industries');

	if(!empty($profile->professionalHeadline)) {
		$headline[] = Html::chars($profile->professionalHeadline);
	} else {
		$headline[] = '<i>Change professional headline</i>';
	}
	if(!empty($user->country) && isset($contries[$profile->country])) {
		$headline[] = $contries[$profile->country];
	} else {
		$headline[] = '<i>Change country</i>';
	}
	if(!empty($profile->industry) && isset($industries[$profile->industry])) {
		$headline[] = $industries[$profile->industry];
	} else {
		$headline[] = '<i>Change industry</i>';
	}
	$headline = implode(' | ', $headline);

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
?>

<div class="block-userinfo is-edit">
	<div class="userinfo-left">
			<?= View::factory('pages/profile/edit/ava-block', array(
				'profile' => $profile
			)); ?>
		<a href="<?= Request::generateUri('profile', 'editUserInfo') ?>" onclick="web.blockProfileEdit(); return web.ajaxGet(this);" class="userinfo-viewcontact btn-roundblue-border icons i-editcustom" title="">Edit contact info</a>
		<a href="<?= Request::generateUri('profile', 'index') ?>" class="userinfo-saveandclose btn-roundblue-border icons i-editcustom active" title="Done editor"><div class="icons i-editround"><span></span></div> Finish editing</a>
	</div><div class="userinfo-right">
		<div class="userinfo-name"><?= nl2br(HTML::chars($profile->firstName . ' ' . $profile->lastName)) ?><a href="<?= Request::generateUri('profile', 'editUserName')?>" onclick="web.blockProfileEdit(); return web.ajaxGet(this);" class="btn-roundblue-border icons i-editcustom" title="Edit user first and second name"><span></span>Edit</a></div>
		<div class="userinfo-headline bg-blue"><a href="<?= Request::generateUri('profile', 'editHeadline')?>" onclick="web.blockProfileEdit(); return web.ajaxGet(this);" class="btn-roundblue-border icons i-editcustom " title="Edit user headline"><span></span>Edit</a><?= $headline ?></div>
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
	</div>

	<div class="userinfo-contactinfo <?= (!empty($profile->alias) || !empty($profile->email2) || $profile->phone || !empty($profile->fullAddress) || !empty($websites)) ? 'opened' : null ?> ">
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
	<? if(isset($countConnections)) : ?>
		<div class="userinfo-countconnections">Connections <?= $countConnections ?></div>
	<? endif; ?>
</div>

