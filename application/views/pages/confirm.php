<div class="col-md-11  ">
    <div class="white-block">
        <h2 class="title">
            Last step for registration!
        </h2>
        <? $f_registration->header(); ?>
        <div class="home-form-regfields">
            <? $f_registration->render('fields-2'); ?>
            <? $f_registration->render('submit'); ?>
        </div>
        <? $f_registration->footer(); ?>
    </div>
</div>