<div class="profile-register ">
	<div class="eva-slider-content-outer hide">
		<a eva-content="Save changes" class="btn btn-ok content-box-edit" href="">Save changes</a>
		<div class="eva-slider-content">
			<div class="eva-slider-content-inner">
				<div class="fieldset" data-name="add-fieldset-name">
					<div class="row" data-name="add-name">
						<div class="label">Name</div>
						<div class="value">
							<span data-name="add-firstName"><?=$values['firstName'] ?></span>
							<span data-name="add-lastName"><?=$values['lastName'] ?></span>
						</div>
					</div>
					<div class="row" data-name="add-gender">
						<div class="label">Gender</div>
						<div class="value">
						<span data-name="add-gender"><?=$values['gender'] ? t('gender.'.$values['gender']) : t('gender.M') ?></span>
						</div>
					</div>
					
					<span class="edit-fieldset" href=""></span>
				</div>
				<div class="fieldset" data-name="add-fieldset-login">
					<div class="row">
						<div class="label">Login</div>
						<div class="value">
							<span data-name="add-name"><?=$values['name'] ?></span>
						</div>
					</div>
					
					<? if(isset($form)) : ?>
					<div class="row">
						<div class="label">Password</div>
						<div class="value">
							<span data-name="add-password"><?=preg_replace('/./', '*', $values['password']) ?></span>
							<span data-name="add-passwordConfirm" style="display: none" ></span>
						</div>
					</div>
					<? endif; ?>
					
					<span class="edit-fieldset" href=""></span>
					
				</div>
				<div class="fieldset" data-name="add-fieldset-contacts">
					<div class="row">
						<div class="label">E-mail</div>
						<div class="value">
							<span data-name="add-email"><?=$values['email'] ?></span>
						</div>
					</div>
					<div class="row">
						<div class="label">Phone</div>
						<div class="value">
							<span data-name="add-phone"><?=$values['phone'] ?></span>
						</div>
					</div>
					
					<span class="edit-fieldset" href=""></span>
				</div>
				<? if(isset($form->elements['building_id'])):?>
					<div class="fieldset" data-name="add-fieldset-location">
						<div class="row">
							<div class="label">Building</div>
							<div class="value">
								<span data-name="add-building_id"><?=$values['building_id'] ?></span>
							</div>
						</div>
						<div class="row">
							<div class="label">Unit</div>
							<div class="value">
								<span data-name="add-unit_id"><?=$values['unit_id'] ?></span>
							</div>
						</div>
						
						<span class="edit-fieldset" href=""></span>
					</div>
				<? endif; ?>
			</div>
		</div>

		<div class="none">
			<?=$form ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	evaPosition = 'edit';
</script>