<?// dump($countInSearch, 1); ?>
<?// dump($countVisits, 1); ?>

<div class="block-whoviewprofile">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>Who's viewed your profile</div>
	</div>
	<ul>
		<li>
			<a href="<?= Request::generateUri('profile', 'statistic') ?>" class="">
				<div class="whoviewprofile-count"><?= $countVisits ?></div>
				<div class="whoviewprofile-text">Your profile has been viewed by <span><?= $countVisits ?></span> people in the past <span>15</span> days.</div>
			</a>
		</li>
		<li>
			<div>
				<div class="whoviewprofile-count"><?= $countInSearch ?></div>
				<div class="whoviewprofile-text">Your profile has shown up in search results <span><?= $countInSearch ?></span> times in the past <span>30</span> days.</div>
			</div>
		</li>
	</ul>
</div>


