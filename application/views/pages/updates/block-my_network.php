<?// dump($my_network, 1); ?>
<?
	$total = 0;
	if(isset($user->countConnections)) {
		$total += $user->countConnections;
	}
	if(isset($user->countConnections2)) {
		$total += $user->countConnections2;
	}
	if(isset($user->countConnections3)) {
		$total += $user->countConnections3;
	}
?>

<div class="block-my_network">
	<div class="content-title">
		<div>My network</div>
	</div>

	<div class="my_network-outer">
		<div class="my_network-border"></div>
		<a class="my_network-first" href="<?= Request::generateUri('search', 'people') . '?connection=1' ?>">
			<div class="my_network-count" ><?= (isset($user->countConnections)) ? $user->countConnections : '?' ?></div>
			<div class="my_network-title" >connections</div>
		</a>
		<a class="my_network-second is-down" href="<?= Request::generateUri('search', 'people') . '?connection=2' ?>">
			<div class="my_network-count" ><?= (isset($user->countConnections2)) ? $user->countConnections2 : '?' ?></div>
			<div class="my_network-title" >2nd connections</div>
		</a>
		<a class="my_network-third is-down" href="<?= Request::generateUri('search', 'people') . '?connection=3' ?>">
			<div class="my_network-count" ><?= (isset($user->countConnections3)) ? $user->countConnections3 : '?' ?></div>
			<div class="my_network-title" >3rd connections</div>
		</a>
	</div>
	<a href="<?= Request::generateUri('search', 'people') . '?connection=1,2,3' ?>" class="my_network-totalcount">
		<div><?= $total ?> Total</div>
	</a>

	<a class="btn-roundblue my_network-add_conections" href="<?= Request::generateUri('connections', 'invite') ?>">
		Add connections
	</a>

</div>

