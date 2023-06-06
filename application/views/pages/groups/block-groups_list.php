<?// dump($groups_joined, 1); ?>

<? $i = 0; ?>

<div class="block-groups_list">
	<div class="title-big">See what's new in your groups<span>(<?= count($groups_joined['data']) ?>)</span></div>
	<ul class="list-items">
		<? foreach($groups_joined['data'] as $group) : $i++ ?><li data-id="group_block_<?= $group->id ?>">
			<?= View::factory('pages/groups/item-join', array(
			'group' => $group
		)) ?>
			</li><? if($i == 4) : $i = 0; ?><li>
			</li><? endif ?><? endforeach ?><? if($i != 0) : ?><li>
		</li><? endif ?>
	</ul>
</div>