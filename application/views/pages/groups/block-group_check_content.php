<?// dump($contents, 1); ?>
<?// dump($group, 1); ?>
<?// dump($contents, 1); ?>

<div class="block-group_check_content">
	<? if(count($contents['data']) != 0) : ?>
		<div class="checkbox-control" data-id="1" data-list=".checkbox-control-select" data-select_label="Select all">
			<a href="<?= Request::generateUri('groups', 'acceptDiscussions', $group->id) . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-accept icon-text btn-icon hidden" ><span></span>Accept</a>
			<a href="<?= Request::generateUri('groups', 'deleteDiscussions', $group->id) . Request::getQuery(); ?>" onclick="return box.confirm(this, true);" class="icons i-delete icon-text btn-icon hidden" ><span></span>Delete</a>
		</div>
		<div class="title-big">Check content</div>

		<div class="block-list-ckeck_content block-list-updates">
			<ul class="list-items">
				<? if($contents) : ?>
					<li class="hidden"></li>
					<? foreach($contents['data'] as $timeline) : ?>
						<?= View::factory('pages/updates/item-update', array(
							'timeline' => $timeline,
							'isUsernameLink' => TRUE,
							'textLen' => 200,
							'showTimelineType' => FALSE,
							'isEditPanels' => FALSE,
							'isCheck' => TRUE,
							'showLike' => FALSE,
							'showComment' => FALSE,
							'showFollow' => FALSE,
							'showReadMore' => TRUE
						)) ?>
					<? endforeach; ?>
					<li>
						<?= View::factory('common/default-pages', array(
								'isBand' => TRUE,
								'autoScroll' => TRUE
							) + $contents['paginator']) ?>
					</li>
				<? endif; ?>
			</ul>
		</div>
	<? else : ?>
		<div class="list-item-empty">
			No contents
		</div>
	<? endif; ?>

</div>
