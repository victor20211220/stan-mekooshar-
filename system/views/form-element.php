<?php

$errors = array();
if (false === $inline) {

	echo '<li'
		. ($el->grouped ? ' class="autoform-grouped"' : '')
		. (null !== $el->visible ? ' id="' . $form->attributes['id'] . '-field-' . $el->name . '"' : '')
		. (false === $el->visible ? ' style="display: none;"' : '')
		. '><div class="autoform-label" style="padding-top: 1px";'.$labelWidth.'>'
		. '<label for="' . $form->attributes['id'] . '-' . $el->name . '">'
		. ($el->label ? $el->label : '&nbsp;')
		. ($el->hint  ? '<sup title="' . $el->hint . '">'. $form->hintHtml . '</sup>' : '')
		. ($el->required ? '&nbsp;<em>*</em>' : '&nbsp;')
		. ($el->label ? $form->labelSeparator : '')
		. '</label></div><div class="autoform-element" '.$listMargin.' >';

}

// Render element
echo '<div class="autoform-element-inner">';
if ($el->contentTop) {
	echo '<div class="autoform-content-top">' . $el->contentTop . '</div>';
}
if ($el->contentLeft) {
	echo '<div class="autoform-content-left">' . $el->contentLeft . '</div>';
}
echo $el->render();
if ($el->contentRight) {
	echo '<div class="autoform-content-right">' . $el->contentRight . '</div>';
}
if ($el->contentBottom) {
	echo '<div class="autoform-content-bottom">' . $el->contentBottom . '</div>';
}
echo '</div>';

$inline = $el->inline;

if (false === $inline) {
	if (false == $combineErrors) {
		if (false == empty($el->errors)) {
			$errors[] = $el;
		}
		if ($errors) {
			echo '<ul class="autoform-element-errors">';
			foreach ($errors as $i) {
				$id = $i->composeId();
				foreach ($i->errors as $msg) {
					echo '<li><label for="' . $id . '">' . $msg . '</label></li>';
				}
			}
			echo '</ul>';
		}
	}
	echo '</div></li>' . chr(10);
} elseif (false == $combineErrors && false == empty($el->errors)) {
	$errors[] = $el;
}

