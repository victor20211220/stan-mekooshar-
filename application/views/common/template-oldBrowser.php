<!DOCTYPE html>
<!--[if IE 7 ]> <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]> <!--> <html class="no-js" lang="en"> <!-- <![endif]-->
	<head>
		<?
			// If title is not defined then it will contain last part of breadcrumbs.
			$title = isset($title) ? $title : (count($crumbs) ? array_shift(end($crumbs)) : false);
			// Prepend home breadcrumb
			array_unshift($crumbs, array('Home', '/'));
			// Draw head section
			View::factory('common/default-head', array(
				'title'   => (count($title) > 1) ? array(array_shift($title)) : $title,
				'description' => (isset($description)) ? $description : '',
				'keywords' => (isset($keywords)) ? $keywords : '',
				'links'   => $links,
				'scripts' => $scripts
			))->render();
		?>
	</head>
	<body>
		<h2>Old browser</h2>
		<p>Download new version of browser</p>
	</body>
</html>
