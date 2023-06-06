<? // dump($skill, 1); ?>
<? // dump($skill_endorsement, 1); ?>
<? // dump($isConnected, 1); ?>
<?
if(!isset($isConnected)) {
	$isConnected = FALSE;
}
?>

<li class="bg-blue" data-id="skill_<?= $skill->skill_id ?>">
	<?= $skill->skillName ?> <b>| <?= $skill->skillEndorsement ?></b>
	<? if ($skill->user_id != $user->id && $isConnected) : ?>
		<a href="<?= Request::generateUri('profile', 'endorseSkill', array($skill->user_id, $skill->skill_id)) ?>"
		   onclick="return web.ajaxGet(this)"
		   class="icons <?= (isset($skill_endorsement[$skill->skill_id][$user->id])) ? 'i-deleteround' : 'i-add' ?>"><span></span></a>
	<? endif; ?>
	<div class="block-skills-endorment  user-gallery">
		<div class="block-skills-endorment-prev btn-prev icon-prev" onclick="web.prevUserGallery(this);"><span></span>
		</div>
		<ul><? $i = 0;
				if (isset($skill_endorsement[$skill->skill_id])) {
					foreach ($skill_endorsement[$skill->skill_id] as $endorsement) {
						if ($skill->skill_id == $endorsement->skill_id) {

							$i++;
							if ($i == 1) {
								echo '<li>';
							}

							echo '<div>' . View::factory('parts/userava-more', array(
									'ouser'      => $endorsement,
									'keyId'     => 'userId',
									'isTooltip' => TRUE
								)) . '</div>';

							if ($i == 5) {
								$i = 0;
								echo '</li>';
							}
						}
					}
					if ($i != 0) {
						echo '</li>';
					}
				} ?></ul>
		<div class="block-skills-endorment-next btn-next icon-next" onclick="web.nextUserGallery(this);"><span></span>
		</div>
	</div>
</li>
