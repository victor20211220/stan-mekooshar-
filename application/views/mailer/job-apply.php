Subject: New applicant in Mekooshar

<?=new View('mailer/email-header')?>

<div style="padding-left: 25px; padding-right: 15px;">
	<br>
	<h1 style="font-size: 24px;"><span style="color: #129fcd; text-transform: capitalize;"><b>Hi <?= $firstName ?>,</b></span></h1>
	<div>
		You have new applicant in Mekooshar.<br/><br/>
		Company: <?= $company_name ?><br/>
		Job title: <?= $job_title ?><br/>
		Applicant full name: <?= $applicant_full_name; ?><br/>

	</div>
</div>
<br><br><br>
<div style="background-color: #f4f4f4; padding-left: 25px; padding-right: 15px; padding-top: 15px; padding-bottom: 15px;">
	You can preview this applicant using next link:<br/>
	<a href="<?=Url::site('/jobs/applicant/' . $job_id . '/' . $applicant_id . '/')?>"><?=Url::site('/jobs/applicant/' . $job_id . '/' . $applicant_id . '/')?></a>
</div>
<br>

<?=new View('mailer/email-footer')?>