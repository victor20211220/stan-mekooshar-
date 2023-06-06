<img src="<?= $image->urls['url_tiny']; ?>" data-id="<?= $image->id; ?>" width="100" height="100" />

<div class="gallery-image-actions">
	<a href="/uploader/files/editImage/<?= $image->id; ?>/" class="edit">+</a>
	<a eva-confirm href="/uploader/files/removeFromList/<?= $image->id; ?>/" class="remove">+</a>
</div>