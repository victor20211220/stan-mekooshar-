<?// dump($connectionWhoVisitMyProfile, 1); ?>

<div class="statistic-list_people">
	<div class="list_people-title">
		<div class="title-big">Who has viewed your profile</div>
	</div>
	<ul class="list-items">
		<? if(!empty($connectionWhoVisitMyProfile['data'])) : ?>
			<? foreach($connectionWhoVisitMyProfile['data'] as $connectionWhoVisit) : ?>
				<?= View::factory('pages/profile/statistic/item-profile-statistic', array(
					'connectionWhoVisit' => $connectionWhoVisit
				)) ?>
			<? endforeach; ?>
		<? else: ?>
			<li class="list-item-empty">
				No profile
			</li>
		<? endif; ?>
	</ul>
</div>