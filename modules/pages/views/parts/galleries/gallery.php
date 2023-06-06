<?// dump($items, 1); ?>
<?// dump($group, 1); ?>

<ul class="gallery">
	<? foreach($items as $item): ?>
		<? $info = array(); ?>
		<? if(!empty($item->info)) : ?>
			<? $info = unserialize($item->info); ?>
		<? endif; ?>
		<? if($item->group == $group): ?>
			<li><img title="<?= (isset($info['title'])) ? $info['title'] : null ?>" alt="<?= (isset($info['alternative'])) ? $info['alternative'] : null ?>" src="<?= Model_Files::generateUrl($item->token, $item->ext, $item->type, $item->isImage, $item->name, 'preview') ?>"/></li>
		<? endif; ?>
	<? endforeach; ?>
</ul>