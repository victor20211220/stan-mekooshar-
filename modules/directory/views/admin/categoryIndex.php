<div class="content-header">
	<div class="back_button">
		<a eva-content="Go back" class="btn btn-left" title="Go back" href="<?=(!$parentId ? '/admin/' : Request::$controller . ($category->parentId != 0 ? 'browse/'.$section.'/'.$category->parentId.'/' : 'browse/'.$section.'/')) ?>">Back</a>
	</div>
	
	<? if(!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false; $title = ($title) ? $title[0] : 'Dashboard';
	} ?>
	
	<h1><?=Text::short($title) ?></h1>
	
	<? if(!empty($filtersOrder)) : ?>
	<form action="" method="get" class="filters-block">
		<span class="filters-title">Filter by:</span>
		<input type="hidden" name="show" value="<?=$showed ?>" />
		<select name="order">
		<? foreach($filtersOrder as $k => $v) : ?>
			<option <?=(!$v['active']) ? 'disabled=""' : '' ?> <?=$filters['order'] == $k ? 'selected=""' : '' ?> value="<?=$k ?>"><?=$v['title'] ?></option>
		<? endforeach; ?>
		</select>
		
		<select name="dir">
		<? foreach($filtersDir as $k => $v) : ?>
			<option <?=(!$v['active']) ? 'disabled=""' : '' ?> <?=$filters['dir'] == $k ? 'selected=""' : '' ?> value="<?=$k ?>"><?=$v['title'] ?></option>
		<? endforeach; ?>
		</select>
	</form>
	<? endif; ?>
	
	<? if($fullContent) : ?>
	<div class="content-nav">
		<a class="main-btn main-btn-categories <?=($showed == 'categories') ? 'active' : '' ?>" href="<?=Request::$uri . '?show=categories' ?>"><span>Categories</span></a>
		<a class="main-btn main-btn-items <?=($showed == 'items') ? 'active' : '' ?>" href="<?=Request::$uri . '?show=items' ?>"><span>Items</span></a>
	</div>
	<? endif; ?>
	
	<? if($itemData['permissions']['delete'] && !empty($category)) : ?>
	<div class="remove-btn">
		<a eva-confirm="" class="btn btn-remove" href="<?=Request::$controller . 'removeCategory/' . $section . '/' . $category->id . '/' ?>">Remove</a>
	</div>
	<? endif; ?>
</div>
<div class="content-data">
	<div class="content-inner">
<?if ($showed == 'categories') : ?>
	<div class="categories-block">
		<? if ($permisions['add']): ?>
		<a href="<?=Request::$controller . 'addCategory/' . $section . '/' . (isset($parentId) ?  $parentId . '/' : '') ?>" class="add-item">
			<span class="item-type-plus type-category"></span>
			<span class="item-title">
				Add category
			</span>
		</a>
		<? endif; ?>
		
		<? if(isset($paginator['prev']) && $paginator['prev']) : ?>
			<div class="pager"><a class="btn btn-up" href="<?=$paginator['prev'] ?>">prev</a></div>
		<? endif; ?>
		
		<ul class="items-list <?=$permisions['sorting'] ? 'sortable-y' : '' ?>">
			<?=new View('admin/parts/categoryIndexItems', array(
			    'showed' => $showed, 
			    'section' => $section, 
			    'permisions' => $permisions, 
			    'items' => $items, 
			    'contentTitle' => $contentTitle,
			    'modSettings' => $modSettings
			)) ?>
		</ul>
			
		<? if(isset($paginator['next']) && $paginator['next']) : ?>
			<div class="pager"><a class="btn btn-down" href="<?=$paginator['next'] ?>">next</a></div>
		<? endif; ?>
	</div>
<? endif; ?>

<?if ($showed == 'items') : ?>
	<div class="items-block">
		<? if ($permisions['add']): ?>
		<a href="<?=Request::$controller . 'addItem/' . $section . '/' . (isset($parentId) ?  $parentId . '/' : '') ?>" class="add-item">
			<span class="item-type-plus type-item"></span>
			<span class="item-title">
				Add item
			</span>
		</a>
		<? endif; ?>
		
		<? if(isset($paginator['prev']) && $paginator['prev']) : ?>
			<div class="pager"><a class="btn btn-up" href="<?=$paginator['prev'] ?>">prev</a></div>
		<? endif; ?>
		
		<ul class="items-list <?=$permisions['sorting'] ? 'sortable-y' : '' ?>">
			<?=new View('admin/parts/categoryIndexItems', array(
			    'showed' => $showed, 
			    'section' => $section, 
			    'permisions' => $permisions, 
			    'items' => $items, 
			    'contentTitle' => $contentTitle,
			    'modSettings' => $modSettings
			)) ?>
		</ul>
		
		<? if(isset($paginator['next']) && $paginator['next']) : ?>
			<div class="pager"><a class="btn btn-down" href="<?=$paginator['next'] ?>">next</a></div>
		<? endif; ?>
	</div>
<? endif; ?>
</div>

</div>
<div class="content-footer">
	<span class="status">
	<? if(count($items)) : ?>
		Showed: <span class="showed"><?=count($items) ?></span> of <span class="count"><?=$count[$showed] ?></span> <?=$showed ?>
	<? else : ?>
		No items
	<? endif; ?>
	</span>
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

<? if($paginator['page'] == 1) : ?>
<script type="text/javascript">
$(function() {
	var $contentData = $('.content-data');
	var contentDataHeight = $contentData.height();
	
	$('.pager a').click(function() {
		var $this = $(this);
		if($this.hasClass('loading')) {
			return false;
		}
		
		$this.addClass('loading');
		$.ajax({
			type: "POST",
			url: $this.attr('href'),
			data: {'pager': 1},
			dataType: "json",
			success: function(data) {
				if(data.next) {
					$this.attr('href', data.next);
				} else {
					$this.parent().remove();
				}
				$('.items-list').append(data.content);
				if($('.sortable-y').length) {
					$.system.sortable($('.sortable-y'), {'axis': 'y'});
				}
				
				$('.content-footer').find('.showed').text($('.items-list').children().length);
				
				$contentData.trigger('scroll');
				$this.removeClass('loading');
			},
			error: function() {
				$('#ajax-message').html('Error loading content....');
			}
		});
		
		return false;
	});
	
	$contentData.scroll(function() {
		var $child = $(this).children();
		if(contentDataHeight - $child.position().top > $child.outerHeight() - 30) {
			$(this).find('.pager').children('a').click();
		}
	});
	
	$(window).resize(function() {
		contentDataHeight = $contentData.height();
		$contentData.trigger('scroll');
	});
	
	$contentData.trigger('scroll');
});
</script>
<? endif; ?>