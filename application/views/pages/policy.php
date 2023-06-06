<?// dump($page, 1); ?>

<div class="block_page">
	<h1 class="page-header"><?= Html::chars($page->title1); ?></h1>

	<div>
		<?= $page->text ?>
	</div>
	<a href="/" class="btn-roundblue_big" title="Back to Home">Back to Home</a>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$(document).click(function(event){
			$target = $(event.target);

			var $teamPhoto = $('.galleryTeamPhoto');
			if($teamPhoto.size()) {
				var $element = $target.closest('li')
				var hasClassActive = $element.hasClass('active');
				$teamPhoto.find('> li').removeClass('active');

				if($element.size() && !hasClassActive) {
					var id = $element.data('teamid');
					$element.addClass('active');
					$teamPhoto.addClass('showBiographi');
					$teamPhoto.find('.teamPhotoBiography[data-teamid="' + id + '"]').addClass('active');
				} else {
					$teamPhoto.removeClass('showBiographi');
				}
			}
		});
	});
</script>