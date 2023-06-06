<?// dump($isCreateGroups, 1); ?>
<?// dump($myGroups, 1); ?>
<?// dump($groupMembers, 1); ?>
<?// dump($peopleAlsoViewed, 1); ?>
<?// dump($groups_interested, 1); ?>

<div class="block-groups_right_panel">
	<? if(isset($myGroups) && !empty($myGroups['data'])) : ?>
		<?= View::factory('pages/groups/block-manage_your_groups', array(
			'myGroups' => $myGroups
		)) ?>
	<? endif; ?>

	<? if(isset($isCreateGroups) && $isCreateGroups) : ?>
		<?= View::factory('pages/groups/block-create_group_page', array(

		)) ?>
	<? endif; ?>

	<? if(isset($groupMembers) && !empty($groupMembers['data'])) : ?>
		<?= View::factory('pages/groups/block-group_members', array(
			'groupMembers' => $groupMembers
		)) ?>
	<? endif; ?>

	<? if(isset($groups_interested) && !empty($groups_interested['data'])) : ?>
		<?= View::factory('pages/groups/block-interest_groups', array(
			'groups_interested' => $groups_interested
		)) ?>
	<? endif; ?>

	<? if(isset($peopleAlsoViewed) && !empty($peopleAlsoViewed['data'])) : ?>
		<?= View::factory('pages/groups/block-groups_people_also_viewed', array(
			'peopleAlsoViewed' => $peopleAlsoViewed
		)) ?>
	<? endif; ?>
</div>