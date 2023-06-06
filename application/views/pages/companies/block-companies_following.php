<?// dump($myFollowing, 1); ?>

<? $i = 0; ?>

<div class="line"></div>

<div class="block-companies_following">
	<div class="title-big">Companies your are following<span>(<?= count($myFollowing['data']) ?>)</span></div>

	<ul class="list-items">
		<? foreach($myFollowing['data'] as $company) : $i++ ?><li data-id="company_block_<?= $company->id ?>">
				<?= View::factory('pages/companies/item-following', array(
					'company' => $company
				)) ?>
			</li><? if($i == 4) : $i = 0; ?><li>
			</li><? endif ?><? endforeach ?><? if($i != 0) : ?><li>
		</li><? endif ?>
	</ul>
</div>