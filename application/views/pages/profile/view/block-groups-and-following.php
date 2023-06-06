<?// dump($follow_companies, 1); ?>
<?// dump($follow_groups, 1); ?>
<?// dump($profile, 1); ?>

<div class="block-groups-and-following">
	<? if(count($follow_groups['data']) > 0) : ?>
		<div>
			<div class="text-bgtitle">Groups<br></div>
			<ul class="block-groups-and-following_groups">
				<? foreach($follow_groups['data'] as $group) : ?><li data-id="group_<?= $group->id ?>">
					<?= View::factory('parts/groupsava-more', array(
					'group' => $group,
					'avasize' => 'avasize_52',
					'isGroupNameLink' => TRUE,
					'isFollowButton' => ($profile->id == $user->id) ? TRUE : FALSE
				)) ?>
					</li><? endforeach ?><li>
					<?= View::factory('common/default-pages', array(
							'controller' => Request::generateUri('profile', 'getFollowUserGroups', $profile->id),
							'isBand' => TRUE,
							'autoScroll' => FALSE
						) + $follow_groups['paginator']) ?>
					</li>
			</ul>
		</div>
	<? endif ?>
	<? if(count($follow_companies['data']) > 0) : ?>
		<div>
			<div class="text-bgtitle">Following<br></div>
			<ul class="block-groups-and-following_companies">
				<? foreach($follow_companies['data'] as $company) : ?><li data-id="company_<?= $company->id ?>">
						<?= View::factory('parts/companiesava-more', array(
							'company' => $company,
							'avasize' => 'avasize_52',
							'isCompanyIndustry' => TRUE,
							'isCompanyNameLink' => TRUE,
							'isFollowButton' => ($profile->id == $user->id) ? TRUE : FALSE
						)) ?>
					</li><? endforeach ?><li>
					<?= View::factory('common/default-pages', array(
							'controller' => Request::generateUri('profile', 'getFollowUserCompanies', $profile->id),
							'isBand' => TRUE,
							'autoScroll' => FALSE
						) + $follow_companies['paginator']) ?>
				</li>
			</ul>
		</div>
	<? endif ?>
</div>

