<?// dump($post, 1); ?>

<li>
	<div>
		<div class="company_analytics-update">
			<? switch($post->typePost) :
			case POST_TYPE_TEXT: ?>
				<?= substr(Html::chars($post->text), 0, 200) . ((strlen(Html::chars($post->text)) > 200) ? '...' : null) ?>

				<? break;
			case POST_TYPE_IMAGE: ?>
				<img class="company_analytics-img_and_text" src="<?= Model_Files::generateUrl($post->alias, 'jpg', FILE_UPDATES, TRUE, false, 'userava_44') ?>" onclick="box.previewImage(this);" data-img="<?= Model_Files::generateUrl($post->alias, 'jpg', FILE_UPDATES, TRUE, false, 'preview') ?>" />
				<?= substr(Html::chars($post->text), 0, 180) . ((strlen(Html::chars($post->text)) > 180) ? '...' : null) ?>
				<? if(empty($post->text)) : ?>
					<span class="company_analytics-update_only_image">[only image]</span>
				<? endif; ?>

				<? break;
			case POST_TYPE_WEB: ?>
				<? if(!is_null($post->alias)) : ?>
					<img src="<?= Model_Files::generateUrl($post->alias, 'jpg', FILE_UPDATES, TRUE, false, 'userava_44') ?>" onclick="box.previewImage(this);" data-img="<?= Model_Files::generateUrl($post->alias, 'jpg', FILE_UPDATES, TRUE, false, 'preview') ?>" />
				<? endif ?>
					<a class="icons i-link icon-round-min icon-text" href="<?= Html::chars($post->link) ?>" title="" target="_blank"><span></span><?= Html::chars($post->link) ?></a>

				<? break;
			case POST_TYPE_PDF:
			case POST_TYPE_DOC: ?>
				<? if(!is_null($post->alias)) : ?>
					<img src="<?= Model_Files::generateUrl($post->alias, 'jpg', FILE_UPDATES, TRUE, false, 'userava_44') ?>" onclick="box.previewImage(this);" data-img="<?= Model_Files::generateUrl($post->alias, 'jpg', FILE_UPDATES, TRUE, false, 'preview') ?>" />
				<? endif ?>
				<a class="icons i-link icon-round-min icon-text" href="<?= Html::chars($post->link) ?>" title="" target="_blank"><span></span><?= $post->title ?></a>


				<? break;
			endswitch ?>
		</div>
	</div>
	<div><?= date('m-d-Y', strtotime($post->createDate)) ?></div>
	<div><?= $post->countImpressions ?></div>
	<div><?= $post->countClicks ?></div>
	<div><?= ($post->countImpressions != 0) ? round($post->countClicks / $post->countImpressions * 100, 2) : '0' ?>%</div>
</li>
