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
    <div class="content-inner">
	<div class="content-data-inner">
		<p>
			<a class="btn btn-plus" href="<?=Request::generateUri('admin', 'categories', 'add')?>" class="large bold">Add new category</a>
		</p>
		<? if (count($items)): ?>
		<table class="w100 TableWithPadding TableWithBorder table-white" id="userList">
			<thead>
				<tr>
					<th>
						Name
					</th>
					<th class="w15" filter="false">
						Actions
					</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($items['data'] as $item): ?>
					<tr>
						<td>
							<b><?=$item->name ?></b>
						</td>
						<td>
							<? if(!$item->isBlocked) : ?>
								<a eva-content="Edit plan" class="nav-btn main-btn-edit" href="<?=Request::generateUri('admin', 'categories', array('edit', $item->id)) ?>"><span>Edit category</span></a>
								<a eva-content="Remove this order" eva-confirm="" class="nav-btn main-btn-remove" href="<?=Request::generateUri('admin', 'categories', array('remove', $item->id))?>"><span>Remove</span></a>
							<? else: ?>
								<span>blocked</span>
							<? endif; ?>
						</td>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>
		<? endif; ?>
	</div>
</div>
</div>
<div class="content-footer">
	<span class="status">
	<? if(count($items['data'])) : ?>
		Total: <span class="showed"><?=count($items['data']) ?></span> orders
	<? else : ?>
		No orders
	<? endif; ?>
	</span>
</div>