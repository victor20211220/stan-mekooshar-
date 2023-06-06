
<?php

if (false === function_exists('render')) {

	function render($fieldset, $form)
	{
		$inlineElements = array();
		$v = $fieldset->labelWidth ?: $form->labelWidth;
		$labelWidth = ($v ? ' style="width: ' . $v . '"' : '');
		$listMargin = ($v ? ' style="margin-left: ' . $v . '"' : '');
		$attributes = $fieldset->attributes;
		$attributes['id'] =  $form->attributes['id'] . '-fieldset-' . $fieldset->name;
		echo  '<fieldset' . Html::attributes($attributes) . '>'
			. ($fieldset->legend ? '<legend>' . $fieldset->legend . '</legend>' :  '');

		$combineErrors = $fieldset->combineErrors ? true : (null === $fieldset->combineErrors && $form->combineErrors) ? true : false;
		if ($combineErrors || $fieldset->errors) {
			$list = array();
			if ($combineErrors) foreach($fieldset->elements as $el) {
				if (!empty($el->errors)) {
					$list[] = $el;
				}
			}
			if ($list || $fieldset->errors) {
				echo '<ul class="autoform-errors">';
				foreach ($list as $el) {
					$id = $el->composeId();
					foreach ($el->errors as $msg) {
						echo '<li><label for="' . $id . '">' . $msg . '</label></li>';
					}
				}
				foreach ($fieldset->errors as $msg) {
					echo '<li>' . $msg . '</li>';
				}
				echo '</ul>';
			}
		}

		echo '<ol>' . chr(10);

		$inline = false;

		foreach($fieldset->elements as $el) {
			if (false === $el instanceof System_Form_Field_Hidden) {
				$inlineElements[] = $el;

				$inline = $el->inline;

				if($inline === FALSE) {
					foreach($inlineElements as $key => $el1) {
						if($key != 0 && $key < count($inlineElements)) {
							$inlineEl = TRUE;
						} else {
							$inlineEl = FALSE;
						}
//						dump($inlineEl);
						View::factory('form-element', array(
							'inline' => $inlineEl,
							'el' => $el1,
							'form' => $form,
							'labelWidth' => $labelWidth,
							'listMargin' => $listMargin,
							'combineErrors' => $combineErrors,
						))->render();
					}
					$inlineElements = array();
				}



//				if (false === $inline) {
//					$errors = array();
//					echo '<li'
//						. ($el->grouped ? ' class="autoform-grouped"' : '')
//						. (null !== $el->visible ? ' id="' . $form->attributes['id'] . '-field-' . $el->name . '"' : '')
//						. (false === $el->visible ? ' style="display: none;"' : '')
//						. '><div class="autoform-label" '.$labelWidth.'>'
//						. '<label for="' . $form->attributes['id'] . '-' . $el->name . '">'
//						. ($el->label ? $el->label : '&nbsp;')
//						. ($el->hint  ? '<sup title="' . $el->hint . '">'. $form->hintHtml . '</sup>' : '')
//						. ($el->required ? '&nbsp;<em>*</em>' : '&nbsp;')
//						. ($el->label ? $form->labelSeparator : '')
//						. '</label></div><div class="autoform-element" '.$listMargin.' >';
//				}
//
//				// Render element
//				echo '<div class="autoform-element-inner">';
//				if ($el->contentTop) {
//					echo '<div class="autoform-content-top">' . $el->contentTop . '</div>';
//				}
//				if ($el->contentLeft) {
//					echo '<div class="autoform-content-left">' . $el->contentLeft . '</div>';
//				}
//				echo $el->render();
//				if ($el->contentRight) {
//					echo '<div class="autoform-content-right">' . $el->contentRight . '</div>';
//				}
//				if ($el->contentBottom) {
//					echo '<div class="autoform-content-bottom">' . $el->contentBottom . '</div>';
//				}
//				echo '</div>';
//
//				$inline = $el->inline;
//
//				if (false === $inline) {
//					if (false == $combineErrors) {
//						if (false == empty($el->errors)) {
//							$errors[] = $el;
//						}
//						if ($errors) {
//							echo '<ul class="autoform-element-errors">';
//							foreach ($errors as $i) {
//								$id = $i->composeId();
//								foreach ($i->errors as $msg) {
//									echo '<li><label for="' . $id . '">' . $msg . '</label></li>';
//								}
//							}
//							echo '</ul>';
//						}
//					}
//					echo '</div></li>' . chr(10);
//				} elseif (false == $combineErrors && false == empty($el->errors)) {
//					$errors[] = $el;
//				}
			}
		}
		echo '</ol></fieldset>';
	}
}

$render = isset($render) ? $render : null;

if (null === $render || $render === '__header__') {

	echo "\n<form" . Html::attributes($form->attributes) . '>';

	foreach($form->elements as $el) {
		if ($el instanceof System_Form_Field_Hidden) {
			echo $el->render() . chr(10);
		}
	}
}

if (null === $render) {
	foreach ($form->fieldsets as $fieldset) {
		render($fieldset, $form);
	}
} elseif ($render !== '__header__' && $render !== '__footer__') {
	render($form->fieldsets[$render], $form);
}

if (null === $render || $render === '__footer__') {
	echo "</form>\n";
}

/* Local Variables:    */
/* tab-width: 4	       */
/* indent-tabs-mode: t */
/* End:                */




