<?= $form->header(); ?>
<div class="ajaxForm edit-form-outer">
	<?
	if (!isset($title) || !$title) {
		$title = (!empty($crumbs)) ? array_pop($crumbs) : false;
		$title = ($title) ? $title[0] : 'Fill the form';
	}
	?>
	<h1 class="ajax-header"> 
		<span class="main-btn main-btn-<?= $showed ?> active"><span></span></span>
		<?= Html::chars($title) ?>
	</h1>
	<div class="ajax-data">
		<?
		if (isset($multiUpload) && $multiUpload) {
			echo new View('admin/parts/filesUploader', array('type' => $type, 'section' => $section, 'itemId' => $itemId, 'maxSize' => $maxSize, 'ext' => $ext));
			?>
			<div class="uploaded_files"></div>
			<?
		} else {
			echo $form->render('default');
			echo $form->render('customFieldset');
		}
		?>
	</div>
	<div class="ajax-footer">
<?= $form->elements['submit']->render() ?>
	</div>
</div>
<?= $form->footer(); ?>

<? if ($showed == 'videos') : ?>
	<script type="text/javascript">
		$(function() {
			function restore() {
				if ($('#videos-fileDfault').is(':checked')) {
					$('#videos-fileDefault').trigger('click');
				}
				$('#videos-file').closest('li').hide();
				$('#videos-fileDefault').closest('li').hide();
				$('#videos-video-separator').closest('li').hide();
			}

			$('#videos-type').change(function() {
				restore();
				$('#videos-url').val('');
				if ($(this).val()) {
					$('#videos-url').attr('disabled', false);
				} else {
					$('#videos-url').attr('disabled', '');
				}
			});

			if ($('#videos-file').length) {
				function youtube_parser(url) {
					var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
					var match = url.match(regExp);
					if (match && match[7].length == 11) {
						return match[7];
					}

					return false;
				}
				function vimeo_parser(url) {
					var regExp = /\/(\d+).*/;
					var match = url.match(regExp);

					if (match) {
						return match[1];
					}

					return false;
				}

				$('#videos-url').change(function() {
					var $this = $(this);
					var type = $('#videos-type').val();
					var $thumb = $('#videos').find('.thumb');

					$('#videos-url').parent().children('.error').remove();

					$thumb.empty();

					if ($this.val().length == 0) {
						restore();
						return false;
					}

					if (type == 1) {
						var id = youtube_parser($this.val());
					} else {
						var id = vimeo_parser($this.val());
					}

					if (id) {
						$('#videos-file').closest('li').show();
						$('#videos-fileDefault').closest('li').show();
						//				$('#videos-video-separator').closest('li').show();

						if (type == 1) {
							$thumb.append('<img src="http://img.youtube.com/vi/' + id + '/0.jpg" />');
						} else {
							$.ajax({
								type: 'GET',
								url: 'http://vimeo.com/api/v2/video/' + id + '.json',
								jsonp: 'callback',
								dataType: 'jsonp',
								success: function(data) {
									if (data.length) {
										var thumbnail_src = data[0].thumbnail_medium;
										$thumb.append('<img src="' + thumbnail_src + '" />');
									} else {
										$('#videos-url').parent().append('<div class="error red">Url has wrong format</div>');
										restore();
									}
								}
							});
						}
					} else {
						$('#videos-url').parent().append('<div class="error red">Url has wrong format</div>');
						restore();
					}
				});

				$('#videos-fileDefault').change(function() {
					if ($(this).is(':checked')) {
						$('#videos-file').closest('li').hide();
						//				$('#videos-video-separator').closest('li').hide();
					} else {
						$('#videos-file').closest('li').show();
						//				$('#videos-video-separator').closest('li').show();
					}
				});

				$('#videos-url').trigger('change');
			}
		})
	</script>

<? endif; ?>