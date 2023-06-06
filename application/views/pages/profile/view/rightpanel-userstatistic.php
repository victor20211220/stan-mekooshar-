<?// dump($countInSearch, 1); ?>
<?// dump($countVisits, 1); ?>
<?// dump($connectionsMayKnow, 1); ?>
<?// dump($connectionsAlsoViewed, 1); ?>

<?// dump($statisticByMonth, 1); ?>
<?// dump($statisticByWeek, 1); ?>
<?// dump($statisticByDay, 1); ?>
<?// dump($f_Profile_ChangeGpaphStatistic, 1); ?>

<?// dump($rightBanner, 1); ?>
<?// dump($rightBannerBottom, 1); ?>



<div class="block-userstatistic">
	<? if(!empty($rightBanner)) : ?>
		<div class="userprofile-right_banner">
			<?= $rightBanner; ?>
		</div>
	<? endif; ?>
	<? if(isset($countInSearch) || isset($countVisits)) : ?>
		<?= View::factory('pages/profile/view/block-who-view-profile', array(
			'countInSearch' => $countInSearch,
			'countVisits' => $countVisits
		)) ?>
	<? endif; ?>

	<? if(isset($connectionsMayKnow) && !empty($connectionsMayKnow['data'])) : ?>
		<?= View::factory('pages/profile/view/block-people-you-may-know', array(
			'connectionsMayKnow' => $connectionsMayKnow
		)) ?>
	<? endif; ?>


	<? if(isset($connectionsAlsoViewed) && !empty($connectionsAlsoViewed['data'])) : ?>
		<?= View::factory('pages/profile/view/block-people-also-view', array(
			'connectionsAlsoViewed' => $connectionsAlsoViewed
		)) ?>
	<? endif; ?>


	<? if(isset($statisticByMonth) && isset($statisticByWeek) && isset($statisticByDay)) : ?>
		<?= View::factory('pages/profile/view/block-profile-visits-graph', array(
			'statisticByMonth' => $statisticByMonth,
			'statisticByWeek' => $statisticByWeek,
			'statisticByDay' => $statisticByDay,
			'f_Profile_ChangeGpaphStatistic' => $f_Profile_ChangeGpaphStatistic
		)) ?>
	<? endif; ?>
	<? if(!empty($rightBannerBottom)) : ?>
		<div class="userprofile-right_banner">
			<?= $rightBannerBottom; ?>
		</div>
	<? endif; ?>
</div>