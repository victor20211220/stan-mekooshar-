<div id="fb-root"></div>
<div class="home-page">
	<div class="block_page container" >
		<div class="row">
			<div class="col-md-12 col-md-offset-0">
				<div class="white-block">
					<h2 class="title">
						Welcome to Mekooshar
					</h2>
					<div class="text welcome-banner">
						<?= $page->text; ?>
					</div>

				</div>
			</div>

			<div class="home-form">
                <?
                if(isset($_SESSION['socials']['id'])) {
                      View::factory('pages/confirm', array('f_registration' => $f_registration->form))->render();
                }else {
                     View::factory('pages/home-form', array('f_registration' => $f_registration->form))->render();
                }
                ?>
			</div>

		</div>
		<div class="row">
			<div class="col-xs-24" >
				<div class="search-block">
					<span class="title">Find a colleague</span>
					<?= $f_findshort->header();?>
					<?= $f_findshort->render('fields');?>
					<?= $f_findshort->render('submit');?>
					<?= $f_findshort->footer();?>
				</div>
			</div>
		</div>

	</div>
</div>

