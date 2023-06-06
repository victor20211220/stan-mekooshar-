<div class="content-iframe file-manager">
	<h1 class="ajax-header"> 
		<span class="main-btn main-btn-items active"><span></span></span>
		Select file
	</h1>
	<div class="content-nav">
		<a href="#list-attachments" class="main-btn main-btn-attachments2 active" eva-content="Manage files"><span><span>Attachments</span></span></a>
		<a href="#list-images" class="main-btn main-btn-images" eva-content="Manage images"><span>Images</span></a>
	</div>
	<div class="ajax-data">
		<div id="files-list">
			<div id="list-attachments" class="active">
				<? if(!empty($files['attachments'])) : ?>
				<ul class="items-list attachments-list">
				<? foreach($files['attachments'] as $item) : ?>
					<li>
						<div class="item element" data-href="<?=Model_Directoryattachment::src($item); ?>">
							<div class="item-type"></div>
							<div class="item-title">
								<span>
									<?=Text::short(Html::chars($item->name), 60) ?> (<?=Html::chars($item->filename) ?>)
								</span>
							</div>
						</div>
					</li>
				<? endforeach; ?>
				</ul>
				<? else : ?>
				<p><i>No attachments</i></p>
				<? endif; ?>
			</div>
			<div id="list-images">
				<? if(!empty($files['images'])) : ?>
				<ul>
				<? foreach($files['images'] as $item) : ?>
					<li>
						<div class="thumb element" data-href="<?=Model_Directoryimage::src($item, 'content'); ?>" >
							<img src="<?=Model_Directoryimage::src($item, 'tiny') ?>" alt="<?=Html::chars($item->name) ?>" title="<?=Html::chars($item->name) ?>" />
						</div>
					</li>
				<? endforeach; ?>
				</ul>
				<? else : ?>
				<p><i>No images</i></p>
				<? endif; ?>
			</div>
		</div>
	</div>
	<div class="ajax-footer">
		<a eva-content="Please select file at first" href="#" class="btn btn-ok disabled selectButton">OK</a>
	</div>
</div>


