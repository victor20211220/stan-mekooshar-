<?// dump($job, 1); ?>
<?// dump($applicant, 1); ?>
<?// dump($files, 1); ?>

<div class="block-applicant">
	<div class="title-big">Applicant detail information</div>
	<div class="line"></div>
	<div class="applicant-topinfo">
		<?= View::factory('/pages/jobs/block-applicant_buttons', array(
			'job' => $job,
			'applicant' => $applicant,
			'from' => 'applicant'
		)); ?>
		<?= View::factory('parts/userava-more', array(
			'ouser' => $applicant,
			'avasize' => 'avasize_52',
			'isCustomInfo' => TRUE,
			'isLinkProfile' => FALSE,
			'isUsernameLink' => TRUE
		))?>
	</div>
	<div class="applicant-info">
		<div class="applicant-info_left">
			<div class="title-big">Contact information</div>
			<div><b>Email: </b><?= (!empty($applicant->email2)) ? $applicant->email2 : 'none'?></div>
			<div><b>Phone: </b><?= (!empty($applicant->phone)) ? $applicant->phone : 'none'?></div>
		</div>
		<div class="applicant-info_right">
			<div class="title-big">Skills</div>
			<? if(!empty($applicant->profileSkills)) : ?>
				<ul>
					<? $skills = explode(',', $applicant->profileSkills); ?>
					<? foreach($skills as $skill) : ?>
						<li><?= $skill ?></li>
					<? endforeach; ?>
				</ul>
			<? else: ?>
				<div>No skills</div>
			<? endif; ?>
		</div>
	</div>
	<div>
		<div class="title-big">Cover letter</div>
		<div><?= $applicant->jobapplyCoverLetter ?></div>
		<ul class="applicant-filelink">
			<? foreach($files['data'] as $file) : ?>
				<li>
					<a class="icons i-viewblue icon-text" href="<?= Request::generateUri('download', 'apply', array($job->id, $file->token))?>" target="_blank"><span></span><?= $file->name ?></a>
				</li>
			<? endforeach; ?>
		</ul>
	</div>
	<a class="btn-save icon-prev" href="<?= Request::generateUri('jobs', 'applicants', $job->id) ?>" ><span></span>Go back to applicants</a>
</div>
