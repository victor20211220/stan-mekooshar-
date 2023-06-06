<?// dump($companyUpdates, 1); ?>

<?// dump($impressions, 1); ?>
<?// dump($impressionsUnique, 1); ?>
<?// dump($clicks, 1); ?>
<?// dump($likes, 1); ?>
<?// dump($comments, 1); ?>
<?// dump($engagement, 1); ?>
<?// dump($compareCompanies, 1); ?>

<div class="block-company_analytics">
	<div class="title-big">Updates</div>

	<ul class="list-items is_table">
		<li class="list-items-title">
			<div>Update</div>
			<div>Date</div>
			<div>Impressions</div>
			<div>Clicks</div>
			<div>Engagement</div>
		</li>
		<? foreach($companyUpdates['data'] as $post) : ?>
			<?= View::factory('pages/companies/item-analytic_update', array(
				'post' => $post
			)) ?>
		<? endforeach; ?>
		<li>
			<div colspan="5">
				<?= View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => FALSE
					) + $companyUpdates['paginator']) ?>
			</div>
		</li>
	</ul>

	<?= View::factory('pages/companies/block-company_analytics_rich', array(
		'impressions' => $impressions,
		'impressionsUnique' => $impressionsUnique,
		'clicks' => $clicks,
		'likes' => $likes,
		'comments' => $comments,
		'engagement' => $engagement
	)) ?>

	<?= View::factory('pages/companies/block-company_analytics_compare', array(
		'compareCompanies' => $compareCompanies
	)) ?>
</div>