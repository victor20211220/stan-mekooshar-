<div class="content-header">
	<div class="back_button">
		<a eva-content="Go back" class="btn btn-left" title="Go back" href="<?=$backUrl ?>">Back</a>
	</div>
	<? if(!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false; $title = ($title) ? $title[0] : 'Website settings';
	} ?>
	<h1><?=$title ?></h1>
</div>
<div class="content-data without-footer">
    <div class="content-inner">
	<div class="content-data-inner">
		<?=$form ?>

		<? if(isset($options) and count($options)) : ?>
		<h2>Options</h2>
		<ul class="items-list settings-list sorting-y">
			<? foreach ($options as $option): ?>
			<li id="settings-<?=$option->key ?>">
				<div class="sortable-handler"></div>
				<div class="item">
					<div class="item-actions">
						<a class="nav-btn main-btn-text" href="<?=Request::$controller . 'edit/' . $option->key . '/' ?>"><span>Edit</span></a>
						<a class="nav-btn main-btn-remove" eva-confirm="" href="<?=Request::$controller . 'remove/' . $option->key . '/' ?>"><span>Remove</span></a>	
					</div>
					<div class="item-title">
						<span><?=Html::chars($option->name)?></span>
					</div>
				</div>
			</li>
			<? endforeach; ?>
		</ul>
		
		<p>
			<a class="btn btn-plus" href="<?=Request::$controller . 'add/' ?>">Add option</a>
		</p>
		
		<script type="text/javascript">
			$.system.sortable($('.sorting-y'), {'axis': 'y'});
		</script>
		<? endif; ?>
	</div>
</div>
</div>