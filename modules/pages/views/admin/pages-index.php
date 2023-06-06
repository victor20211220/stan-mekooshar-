<div class="content-header">
	<div class="back_button">
		<a eva-content="Go back" class="btn btn-left" title="Go back" href="<?= isset($backLink) ? $backLink : '/admin/' ?>">Back</a>
	</div>
	
	<? if(!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false; $title = ($title) ? $title[0] : 'Dashboard';
	} ?>
	
	<h1><?=Text::short($title) ?></h1>

</div>
<div class="content-data">
	<div class="content-inner">
		<?= $content ?>
	</div>
</div>

<? if(isset($isItem)) : ?>
	<?= View::factory('parts/file-uploader', array(
		'initFunction' => 'contentUploaderList',
		'parentId' => $itemId,
		'type' => FILE_PAGES
	)); ?>
<? endif; ?>


<div class="content-footer">
	<span class="status"></span>
</div>


<script type="text/javascript">
	$(function() {
		if($('.items-list').children('li').length > 2 && $('.add-item').length) {
			$('.content-footer').prepend($('.add-item').clone());
		}
		$('.filters-block').find('select').change(function() {
			$(this).closest('form').submit();
		});
	});
</script>