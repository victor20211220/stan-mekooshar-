<?// dump($items, 1); ?>
<?// dump($group, 1); ?>
<?
$user_info = array();
$i = 0;
?>

<? if(count($items) != 0): ?>
	<span class="line"></span>

	<ul class="gallery-team-photo galleryTeamPhoto">
		<? foreach($items as $item): ?>
			<? $info = unserialize($item->info); ?>
			<? if(!empty($item->info)) : ?>
				<? $info = unserialize($item->info); ?>
			<? endif; ?>
			<? if($item->parent_id == $group): ?>
				<? $i++; ?>
				<? $user_info[$i] = nl2br(Html::chars($info['memberDescription'])); ?>
				<li data-teamid="<?= $i ?>">
					<div class="team-photo-photo">
						<img title="<?= (isset($info['title'])) ? $info['title'] : null ?>" alt="<?= (isset($info['alternative'])) ? $info['alternative'] : null ?>" src="<?= Model_Files::generateUrl($item->token, $item->ext, $item->type, $item->isImage, $item->name, 'aboutus') ?>"/>
						<div>
							<div class="team-photo-hoverinfo">
								<div>Read full <br>biographi</div>
							</div>
						</div>
					</div>
					<div class="team-photo-info">
						<div class="team-photo-threepoints">
							<div></div>
							<div></div>

						</div>
						<div class="team-photo-name"><?= $info['memberName']; ?></div>
						<div class="team-photo-title"><?= $info['memberTitle']; ?></div>
					</div>
				</li>
			<? endif; ?>
			<? if(count($user_info) == 3) : ?>
				<? $j = 0; ?>
				<? foreach($user_info as $key => $value) : $j++; ?>
					<li class="team-photo-biography row-<?= $j ?> teamPhotoBiography" data-teamid="<?= $key ?>">
						<div>
							<?= $value ?>
						</div>
					</li>
				<? endforeach; ?>
				<? $user_info = array(); ?>
			<? endif; ?>
		<? endforeach; ?>

		<? if(count($user_info) != 0) : ?>
			<? $j = 0; ?>
			<? foreach($user_info as $key => $value) : $j++; ?>
				<li class="team-photo-biography row-<?= $j ?> teamPhotoBiography" data-teamid="<?= $key ?>">
					<div>
						<?= $value ?>
					</div>
				</li>
			<? endforeach; ?>
		<? endif; ?>
	</ul>
<? endif; ?>