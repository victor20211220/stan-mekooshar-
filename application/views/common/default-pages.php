<?php
if (!isset($controller)) {
	$controller = Request::$uri;
} else {
	if(!empty($next)) {
		$next = str_replace(Request::$uri, $controller, $next);
	}
	if(!empty($prev)) {
		$prev = str_replace(Request::$uri, $controller, $prev);
	}
}
?>

<? if(isset($isBand) && $isBand) : ?>
	<div id="paginator">

		<? if (isset($next) and $next and !$isLast) : ?>
			<a class="btn btn-down <?= (isset($autoScroll) && $autoScroll) ? 'paginatorAutoScroll' : null ?>" href="<?= $next ?>" onclick="return system.ajaxGet(this);" >show more</a>

			<script type="text/javascript">
				$(document).ready(function(){
					if(typeof web === 'object') {
						web.autoScroll('.paginatorAutoScroll');
					} else {
						system.autoScroll('.paginatorAutoScroll');
					}
				});
			</script>
		<? endif; ?>
	</div>
<? elseif (isset($page) && $pages > 1) : ?>
	<div id="paginator">
		<? if (isset($prev) and $prev and !$isFirst) : ?>
			<a class="getMore btn btn-up" href="<?= Request::$uri . '?' . http_build_query($params + array('page' => $page - 1)) ?>"></a>
		<? endif; ?>

		<ul >
			<?php
			/**
			 * Expect variables:
			 *   $pages	Count of pages
			 *   $page	Current page number (1..$count)
			 * -- $uri	Page template with %page% pattern
			 *   $count      Count records (Кількість записів)
			 *   $isSimple
			 *   $isLast     Bool is the last page (Чи остання сторінка)
			 *   $isFirst    Bool is the first page (Чи перша сторінка)
			 *   $onPage     Count record on page (Кількість записів на сторінці)
			 *   $params     Other parameters (Інші параметри)
			 *   $prev       ULR on the previous existing page (URI посилання на попередню існуючу сторінку)
			 *   $next       ULR on the next existing page (URI посилання на наступну існуючу сторінку)
			 */
			$offset = 3;

			// previous pages
			$from = $page - $offset;
			if ($from < 1) {
				$from = 1;
			}

			// next pages
			$to = $page + $offset;
			if ($to > $pages) {
				$to = $pages;
			}



			if ($page >= $offset + 2) {
				if (isset($first)) {
					echo '<li>' . Html::anchor($first, '<span>1</span>') . '</li>';
				} else {
					echo '<li>' . HTML::anchor($controller . Request::getQuery('page', 1), '<span>1</span>', array('title' => 'Page 1', 'alt' => 'Page 1')) . '</li>';
				}
				if ($page >= $offset + 3) {
					echo '<li>' . HTML::anchor($controller . Request::getQuery('page', 2), '<span>2</span>', array('title' => 'Page 2', 'alt' => 'Page 2')) . '</li>';
				}
				if ($page >= $offset + 4) {
					echo '<li  class="passive"><span class="passive">...</span></li>';
				}
			}

			for ($i = $from; $i < $page; $i++) {
				if ($i == 1) {
					if (isset($first)) {
						echo '<li>' . Html::anchor($first, '<span>1</span>') . '</li>';
						//print($first);
					} else {
						echo '<li>' . HTML::anchor($controller . Request::getQuery('page', 1), '<span>1</span>', array('title' => 'Page 1', 'alt' => 'Page 1')) . '</li>';
					}
				} else {
					echo '<li>' . HTML::anchor($controller . Request::getQuery('page', $i), '<span>' . $i . '</span>', array('title' => 'Page ' . $i, 'alt' => 'Page ' . $i)) . '</li>';
				}
			}

			// current page
			?>

			<?
			//echo '<li class="current"><div title="Current page ' . $page . '" alt="Current page ' . $page . '"></div></li>';
			echo '<li class="current">' . HTML::anchor($controller . Request::getQuery('page', $page), '<span>' . $page . '</span>', array('title' => 'Page ' . $page, 'alt' => 'Page ' . $page)) . '</li>';
			// next four pages

			for ($i = $page + 1; $i <= $to; $i++) {
				echo '<li>' . HTML::anchor($controller . Request::getQuery('page', $i), '<span>' . $i . '</span>', array('title' => 'Page ' . $i, 'alt' => 'Page ' . $i)) . '</li>';
			}
			if ($pages - $to > 0) {
				if ($pages - $to > 2) {
					echo '<li class="passive"><span class="passive">...</span></li>';
				}
				if ($pages - $to > 1) {
					echo '<li>' . HTML::anchor($controller . Request::getQuery('page', $pages - 1), '<span>' . ($pages - 1) . '</span>', array('title' => 'Page ' . ($pages - 1), 'alt' => 'Page ' . ($pages - 1))) . '</li>';
				}
				echo '<li>' . HTML::anchor($controller . Request::getQuery('page', $pages), '<span>' . $pages . '</span>', array('title' => 'Page ' . $pages, 'alt' => 'Page ' . $pages)) . '</li>';
			}
			?>
		</ul>
		<? if (isset($next) and $next and !$isLast) : ?>
			<a class="getMore btn btn-down" href="<?= Request::$uri . '?' . http_build_query($params + array('page' => $page + 1)) ?>"></a>
		<? endif; ?>
	</div>
<? endif; ?>
