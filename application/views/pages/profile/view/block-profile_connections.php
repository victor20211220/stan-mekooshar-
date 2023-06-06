<?// dump($items_connections, 1); ?>
<?// dump($profile, 1); ?>

<div class="block-profile_connections">
	<div class="title-big">Profile connections</div>

	<ul class="profile_connections_all">
		<? foreach($items_connections['data'] as $connection):
			?><?=
				View::factory('pages/profile/view/item-profile_connections', array(
					'connection' => $connection
				));
			?><? endforeach ?><li>
			<?= View::factory('common/default-pages', array(
					'controller' => Request::generateUri('profile', 'getListProfileConnection', $profile->id),
					'isBand' => TRUE,
					'autoScroll' => FALSE
				) + $items_connections['paginator']) ?>
			</li>
	</ul>
</div>


