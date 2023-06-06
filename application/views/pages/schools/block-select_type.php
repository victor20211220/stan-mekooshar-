<?// dump($school, 1); ?>
<?// dump($profile_experiance, 1); ?>
<?// dump($profile_education, 1); ?>
<?// dump($f_Schools_SelectTypeInSchool, 1); ?>

<div class="block-select_type">
	<div class="content-title">
		<div>Select type</div>
	</div>

	<?= $f_Schools_SelectTypeInSchool->form->render(); ?>

<!--	<div class="select_type_btns">-->
<!--		--><?// if(!$profile_education || ($profile_education && $profile_education->yearTo < date('Y') && !is_null($profile_education->yearTo))   ) : ?>
<!--				<a href="--><?//= Request::generateUri('schools', 'setStudent', $school->id) ?><!--" class="blue-btn-big" title="I am a student">-->
<!--					<div>-->
<!--						<span class="title-big">I am</span><br>-->
<!--						a student-->
<!--					</div>-->
<!--			</a>-->
<!--		--><?// endif; ?>
<!--		--><?// if(!$profile_education || $profile_education->yearTo >= date('Y') || is_null($profile_education->yearTo)) : ?>
<!--			<a href="--><?//= Request::generateUri('schools', 'setAlumni', $school->id) ?><!--" class="blue-btn-big" title="I am a alumni">-->
<!--				<div>-->
<!--					<span class="title-big">I am</span><br>-->
<!--					a alumni-->
<!--				</div>-->
<!--			</a>-->
<!--		--><?// endif; ?>
<!---->
<!--		--><?// if($profile_education) : ?>
<!--			--><?// if(is_null($profile_education->yearTo) || $profile_education->yearTo >= date('Y')): ?>
<!--				<div class="select_type-student">-->
<!--					You are a student!-->
<!--				</div>-->
<!--			--><?// else: ?>
<!--				<div class="select_type-alumni">-->
<!--					You are a alumni!-->
<!--				</div>-->
<!--			--><?// endif; ?>
<!--		--><?// endif; ?>
<!---->
<!---->
<!--		--><?// if(!$profile_experiance || $profile_experiance->isSchoolMember === NULL) : ?>
<!--			<a href="--><?//= Request::generateUri('schools', 'setStaffMember', $school->id) ?><!--" class="blue-btn-big select_type-btn_staff" title="Set a faculty of staff member">-->
<!--				<div>-->
<!--					<span class="title-big">Set</span><br>-->
<!--					a faculty of staff member-->
<!--				</div>-->
<!--			</a>-->
<!--		--><?// elseif($profile_experiance->isSchoolMember === '0') : ?>
<!--			<div class="select_type-sentrequest">-->
<!--				Request to staff member is sent!-->
<!--			</div>-->
<!--		--><?// elseif($profile_experiance->isSchoolMember == 1) : ?>
<!--			<div class="select_type-staf_member">-->
<!--				You are faculty of staff member!-->
<!--			</div>-->
<!--		--><?// endif; ?>
<!---->
<!---->
<!--	</div>-->
</div>

