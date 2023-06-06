<?// dump($result, 1); ?>
<?
if($result instanceof Model_User) {
	echo View::factory('pages/search/people/item-search-results', array(
		'result' => $result
	));
} elseif($result instanceof Model_Companies) {
	echo View::factory('pages/search/company/item-search-results', array(
		'company' => $result
	));
} elseif($result instanceof Model_Groups) {
	echo View::factory('pages/search/groups/item-search-results', array(
		'group' => $result
	));
} elseif($result instanceof Model_Universities) {
	echo View::factory('pages/search/school/item-search-results', array(
		'school' => $result
	));
}

