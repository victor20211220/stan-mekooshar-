<?// dump($job, 1); ?>
<?
if(isset($job)) {
	$from = FALSE;
	if(isset($_SESSION['jobs_from'])) {
		$from = $_SESSION['jobs_from'];
	}
	$id = $job->id;
	if(!$from) {
		$from = 'myJobs';
	}
	if(in_array($from, array('myJobs', 'search'))) {
		$id = false;
	}
	$url = Request::generateUri('jobs', $from, $id);
} else {
	$url = Request::generateUri('profile', 'privacySettings');
}
?>


<? if(isset($job)) : ?>
	<div class="block-payment">
		<h4>Thank you! Your job is active.</h4>
		<p>Your order ID is <strong><?=$order->token?></strong>. Please refer to it if you have any questions.</p>
		<a class="btn-roundbrown" href="<?= $url ?>">Back to job</a>
	</div>
<? else: ?>
	<div class="block-payment">
		<h4>Thank you! Your profile is upgraded.</h4>
		<p>Your order ID is <strong><?=$order->token?></strong>. Please refer to it if you have any questions.</p>
		<a class="btn-roundbrown" href="<?= $url ?>">Back to settings</a>
	</div>
<? endif; ?>
