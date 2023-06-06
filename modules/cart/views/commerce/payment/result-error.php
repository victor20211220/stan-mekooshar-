<?// dump($job, 1); ?>

<?
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
?>

<div class="block-payment">
	<h4><strong>Error occured</strong></h4>
	<ul>
		<? foreach ($errors as $error): ?>
			<li><?=$error?></li>
		<? endforeach ?>
	</ul>
	<a class="btn-blue icon-prev" href="<?= Request::generateUri('jobs', $from, $id) ?>"><span></span>Back to job</a>
</div>