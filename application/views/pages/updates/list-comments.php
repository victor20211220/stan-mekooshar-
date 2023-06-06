<?// dump($comments, 1); ?>
<?// dump($f_Updates_AddUpdateComments, 1); ?>
<?// dump($timeline_id, 1); ?>
<?// dump($controller, 1); ?>
<?// dump($autoScroll, 1); ?>

<?
if(!isset($controller)) {
	$controller = Request::generateUri('updates', 'comments', $timeline_id);
}
if(!isset($autoScroll)) {
	$autoScroll = FALSE;
}
?>

<div class="block-list-updatecomments isComments">

	<ul class="list-items">
		<li class="<?= (!$f_Updates_AddUpdateComments) ? 'hidden' : null ?>">
			<? if($f_Updates_AddUpdateComments) : ?>
				<?= View::factory('parts/userava-more', array(
					'avasize' => 'avasize_52',
					'isLinkProfile' => FALSE,
					'isForm' => TRUE,
					'form' => $f_Updates_AddUpdateComments->form,
					'ouser' => clone ($user)
				)) ?>
			<? endif; ?>
		</li>

		<? if(count($comments['data']) == 0 && !$f_Updates_AddUpdateComments) : ?>
			<li>
				<div class="list-item-empty_small">
					No comments
				</div>
			</li>
		<? endif ?>

		<? foreach($comments['data'] as $comment) : ?>
			<?= View::factory('pages/updates/item-comment', array(
				'comment' => $comment
			)) ?>
		<? endforeach; ?>
		<li>
			<?= View::factory('common/default-pages', array(
					'controller' => $controller,
					'isBand' => TRUE,
					'autoScroll' => $autoScroll
				) + $comments['paginator'])
			?>
		</li>
	</ul>

</div>