<?// dump($profile, 1); ?>
<?// dump($f_Connections_AddConnections, 1); ?>

<div class="addconnections">
	<?= $f_Connections_AddConnections->header(); ?>
<!--		<div class="addconnections-title"><b>Invite --><?//= $profile->firstName . ' ' . $profile->lastName ?><!-- to connect your network: </b></div>-->
<!--		<div class="addconnections-tags-title">How well do you know --><?//= $profile->firstName . ' ' . $profile->lastName ?><!--</div>-->
<!--		--><?//= $f_Connections_AddConnections->render('tags') ?>
		<div class="addconnections-message-title"><b>Add message (optional)</b></div>
		<?= $f_Connections_AddConnections->render('message') ?>
		<?= $f_Connections_AddConnections->render('submit') ?>
	    <?= $f_Connections_AddConnections->footer(); ?>
</div>
