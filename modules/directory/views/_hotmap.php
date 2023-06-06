<?='<?xml version="1.0" encoding="UTF-8"?>'?>

<hotmap>
	<image src="<?=Model::factory('directory')->imagePath($image, 'hotmap')?>" />
	<? if (count($hotspots)): ?>
		<? foreach ($hotspots as $hotspot): ?>
			<area id="<?=$hotspot['id']?>" points="<?=$hotspot['points']?>">
				<message>
					<title><?=Html::chars($hotspot['name'])?></title>
					<? if ($hotspot['resolution']): ?>
						<image src="<?=Model::factory('directory')->hotspotPath($hotspot, 'hotspot')?>" />
					<? endif; ?>
					<text><?=Html::chars($hotspot['text1'])?></text>
				</message>
			</area>
		<? endforeach; ?>
	<? endif; ?>
</hotmap>