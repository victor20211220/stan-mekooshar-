<?// dump($jobsYouMayLike, 1); ?>

<? if(!empty($jobsYouMayLike['data'])) : ?>
	<div class="block-jobs_you_may_like">
		<div class="content-title">
			<div>Jobs you may like</div>
		</div>

		<div class="jobs_you_may_like-list">
			<ul>
				<? foreach($jobsYouMayLike['data'] as $job) : ?>
					<li><?= View::factory('parts/companiesava-more', array(
						'company' => $job,
						'avasize' => 'avasize_52',
						'isCompanyIndustry' => TRUE,
						'otherInfo' => $job->title,
						'maxSizeIndustry' => 44,
						'maxSizeOtherInfo' => 88,
						'isLinkJob' => TRUE
					)) ?></li>
				<? endforeach; ?>
			</ul>
		</div>
	</div>
<? endif; ?>

