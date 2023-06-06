<?// dump($complaints, 1); ?>

<div class="content-header">
	<div class="back_button">
		<a class="btn btn-left" title="Go back" href="/admin/users/complaints/">Back</a>
	</div>
	<? if(!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false; $title = ($title) ? $title[0] : 'Dashboard';
	} ?>

	<h1><?=Text::short($title) ?></h1>
</div>

<div class="content-data">
    <div class='content-inner'>
	<div class="content-data-inner">

		<div class="toppanel-statistic">
			<?
			$user = current($complaints['data']);

			$user->userToAlias = trim($user->userToAlias);
			if(!empty($user->userToAlias)) {
				$url_profile = Request::generateUri('profile', $user->userToAlias);
			} else {
				$url_profile = Request::generateUri('profile', $user->userToId);
			}
			?>
			<div><b>User: </b><a href="<?= $url_profile ?>" target="_blank"><?= $user->userToFirstName . ' ' . $user->userToLastName ?></a></div>
			<div><b>Email: </b><?= $user->userToEmail ?></div>
			<br>
			<br>
			<div><b>Actions: </b>
				<a class="user_complaints_unblock <?= (!$user->userToIsBlocked) ? 'hidden' : null ?>" href="<?= Request::generateUri('admin', 'users', array('unblockUserFromUserComplaints', $user->userToId)) ?>"  onclick="return system.ajaxGet(this);">Unblock</a>
				<a class="user_complaints_block <?= (!$user->userToIsBlocked) ? null : 'hidden' ?>" href="<?= Request::generateUri('admin', 'users', array('blockUserFromUserComplaints', $user->userToId)) ?>"  onclick="return system.ajaxGet(this);">Block</a>
			</div>
		</div>
		<br>

		<? if (count($complaints['data'])): ?>
		<table class="w100 TableWithPadding TableWithBorder table-white" id="userList">
			<thead>
				<tr>
					<th filter="false">
						#
					</th>
					<th class="w10">
						User
					</th>
					<th class="w15">
						Email
					</th>
					<th filter="false">
						Complaints description
					</th>
					<th class="w10" filter="false">
						Date complaint
					</th>
					<th class="w10" filter="false">
						Status
					</th>
					<th class="w10" filter="false">
						Action
					</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($complaints['data'] as $complaint): ?>
					<?= View::factory('admin/complaints/item-user-complaints', array(
						'complaint' => $complaint
					)) ?>
				<? endforeach; ?>
				<tr>
					<td colspan="7">
						<?= View::factory('common/default-pages', array(
								'isBand' => TRUE,
								'autoScroll' => TRUE
							) + $complaints['paginator']) ?>
					</td>
				</tr>
			</tbody>
		</table>
		<? endif; ?>
	</div>
</div>
</div>
<div class="content-footer">
	<span class="status">
	<? if(count($complaints['data'])) : ?>
		Total: <span class="showed"><?=$complaints['paginator']['count'] ?></span> c
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