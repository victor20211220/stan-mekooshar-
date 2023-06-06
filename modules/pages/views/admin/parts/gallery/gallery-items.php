<li id="images_<?=$img->id;?>">
	<img src = "<?=$thumb;?>" data-id = "<?=$img->id;?>" width = "100" height = "100" />
	<div class = "gallery-image-actions">
		<a href = "/uploader/files/editImage/<?=$img->id;?>/" class = "edit">+</a>
		<a eva-confirm href = "/uploader/files/removeFromList/<?=$img->id;?>/" class = "remove">+</a>
	</div>
</li>