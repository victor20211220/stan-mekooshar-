<?// dump($users, 1); ?>

<div class="content-header">
	<div class="back_button">
		<a class="btn btn-left" title="Go back" href="/admin/">Back</a>
	</div>
	<? if(!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false; $title = ($title) ? $title[0] : 'Dashboard';
	} ?>

	<h1><?=Text::short($title) ?></h1>
</div>

<div class="content-data">
    <div class='content-inner'>
	<div class="content-data-inner">
		<? if (count($users['data'])): ?>
		<table class="w100 TableWithPadding TableWithBorder table-white" id="userList">
			<thead>
				<tr>
					<th>
						User
					</th>
					<th>
						Email
					</th>
					<th class="w15" filter="false">
						Count complaints
					</th>
					<th class="w10" filter="false">
						Date last complaint
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
				<? foreach ($users['data'] as $user): ?>
					<?= View::factory('admin/complaints/item-allusers-complaints', array(
						'user' => $user
					)); ?>
				<? endforeach; ?>
				<tr>
					<td colspan="6">
						<?= View::factory('common/default-pages', array(
								'isBand' => TRUE,
								'autoScroll' => TRUE
							) + $users['paginator']) ?>
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
	<? if(count($users['data'])) : ?>
		Total: <span class="showed"><?= $users['paginator']['count'] ?></span> c
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