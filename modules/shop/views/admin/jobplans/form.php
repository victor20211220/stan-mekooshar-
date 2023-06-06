<div class="content-header">
	<div class="back_button">
		<a class="btn btn-left" title="Go back" href="<?=Request::$controller ?>">Back</a>
	</div>
	<? if(!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false; $title = ($title) ? $title[0] : 'Dashboard';
	} ?>
	
	<h1><?=Text::short($title) ?></h1>
</div>
<div class="content-data">
    <div class="content-inner">
	<div class="content-data-inner">
		<?=(isset($form) ? $form : '')?>
	</div>
    </div>
</div>
<div class="content-footer">
	
</div>