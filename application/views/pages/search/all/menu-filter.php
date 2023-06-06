<? // dump($f_Search_FilterPeople, 1) ?>
<? // dump($f_Search_FilterCompany, 1) ?>
<? // dump($f_Search_FilterGroup, 1) ?>
<? // dump($f_Search_FilterSchool, 1) ?>
<? // dump($query, 1) ?>
<? // dump($active_menu, 1) ?>
<div class="search-allfilterpanel">
	<?
		echo View::factory('pages/search/people/menu-filter', array(
			'f_Search_FilterPeople' => $f_Search_FilterPeople,
			'query'                 => $query,
			'active_menu'           => $active_menu
		));
		echo View::factory('pages/search/company/menu-filter', array(
			'f_Search_FilterCompany' => $f_Search_FilterCompany,
			'query'                  => $query,
			'active_menu'            => $active_menu
		));
		echo View::factory('pages/search/groups/menu-filter', array(
			'f_Search_FilterGroup' => $f_Search_FilterGroup,
			'query'                => $query,
			'active_menu'          => $active_menu
		));
		echo View::factory('pages/search/school/menu-filter', array(
			'f_Search_FilterSchool' => $f_Search_FilterSchool,
			'query'                 => $query,
			'active_menu'           => $active_menu
		));
	?>
</div>

