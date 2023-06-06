<?// dump($group, 1); ?>

<div class="block-group_delete">
	<div class="title-big">Delete group approve</div>
	<div class="group_delete-question"><b>Do you really want to delete group?</b></div>
	<div>
		<a class="btn-roundbrown" href="<?= Request::generateUri('groups', $group->id) ?>" title="Cancel delete">Cancel</a>
		<a class="btn-roundblue group_delete-delete" href="<?= Request::generateUri('groups', 'remove', $group->id) ?>" onclick="return box.confirm(this);" title="Remove group">Delete group <span class="icons i-denywhite"><span></span></span></a>
	</div>
</div>