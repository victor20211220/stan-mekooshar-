<div class="content-header">
	<div class="back_button">
		<a class="btn btn-left" title="Go back" href="/admin/">Back</a>
	</div>
	<?

    if(!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false; $title = ($title) ? $title[0] : 'Dashboard';
	} ?>
	
	<h1><?=Text::short($title) ?></h1>
</div>
<div class="content-data">
    <div class='content-inner'>
	<div class="content-data-inner">
		<p>
			<a class="btn btn-plus" href="<?=Request::$controller?>add/" class="large bold">Add new user</a>
		</p>
		<? if (count($users['data'])): ?>
		<table class="w100 TableWithPadding TableWithBorder" id="userList">
			<thead>
				<tr>
					<th>
						Name
					</th>
					<th class="w15" filter="false">
						Actions
					</th>
					<th class="w25" filter="false">
						Email
					</th>
					<th class="w20" filter-type='ddl'>
						Role
					</th>
					<th class="w10" filter="false">
						Remove
					</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($users['data'] as $u): ?>
					<tr>
						<td>
							<?=Html::chars($u->lastName . ' ' . $u->firstName)?><br />
							<span class="small"><?=Html::chars($u->name)?></span>
						</td>
						<td>
							<a eva-content="Edit user information" class="nav-btn main-btn-edit" href="<?=Request::$controller.'edit/' . $u->id . '/' ?>"><span>Edit info</span></a>
							<a eva-content="Change password" class="nav-btn main-btn-password" href="<?=Request::$controller.'password/' . $u->id . '/' ?>"><span>Change password</span></a>
						</td>
						<td><a href="mailto:<?=$u->email ?>"><?=Html::chars($u->email) ?></a></td>
						<td><?=Html::chars(ucfirst(strtolower(preg_replace('|([A-Z])|', ' \1', $u->role))))?></td>
						<td>
							<? if($user->id != $u->id): ?>
							<a onclick="return confirm('Are you sure you want to delete this user?');" eva-content="Remove this user" eva-confirm="" class="nav-btn main-btn-remove" href="<?=Request::$controller.'remove/' . $u->id . '/' ?>  "><span>Remove</span></a>
							<? endif; ?>
						</td>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>
		<? if(count($users['data']) > 15) : ?>
		<p>
			<a class="btn btn-plus" href="<?=Request::$controller?>add/" class="large bold">Add new user</a>
		</p>
		<? endif; ?>
		<? endif; ?>
	</div>
</div>
</div>
<div class="content-footer">
	<span class="status">
	<? if(count($users['data'])) : ?>
		Total: <span class="showed"><?=count($users['data']) ?></span> users
	<? else : ?>
		No users
	<? endif; ?>
	</span>
</div>


<script type="text/javascript">
	$(document).ready(function() {
		$('#userList').tableFilter({ selectOptionLabel : 'Show all' });
	});
</script>