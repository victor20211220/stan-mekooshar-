<?php

function render($fieldset, $form)
{
	$attributes = $fieldset->attributes;
	$attributes['id'] =  $form->attributes['id'] . '-row-' . $fieldset->name;
	echo  '<tr' . Html::attributes($attributes) . '>' . chr(10);

	$inline = false;

	foreach($fieldset->elements as $el) {
		if (false === $el instanceof System_Form_Field_Hidden || false === $el->detatched ) {

			if (false === $inline) {
				$errors = array();
				$style  = array();
				if ($el->textAlign) {
					$style[] = 'text-align: ' . $el->textAlign;
				}
				echo  '<td' . (count($style) ? ' style="' . implode(';', $style) . '"' : '') . '>'
					. '<div class="autoform-element">';
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
				echo '</div></td>' . chr(10);
			} elseif (false == empty($el->errors)) {
				$errors[] = $el;
			}
		}
	}
	echo '</tr>';
}


$columns = 0;

echo "\n<form" . Html::attributes($form->attributes) . '><table><thead><tr>';

foreach($form->header->elements as $el) {
	if ($el instanceof System_Form_Field_Hidden && $el->detatched) {
		echo $el->render() . chr(10);
	}
}

$inline = false;
foreach ($form->header->elements as $el) {

	if (
		   ($el instanceof System_Form_Field_Hidden && $el->detatched)
		|| $el instanceof System_Form_Field_Submit
		|| $el instanceof System_Form_Field_Html
	) continue;

	if (false === $inline) {
		$style = array();
		if ($el->width) {
			$style[] = 'width: ' . $el->width;
		}
		if ($el->textAlign) {
			$style[] = 'text-align: ' . $el->textAlign;
		}
		echo  '<th' . (count($style) ? ' style="' . implode(';', $style) . '"' : '') . '>';
	}
	if ($el->label) {
		echo  $el->label
			. ($el->hint  ? '<sup title="' . $el->hint . '">'. $form->hintHtml . '</sup>' : '')
			. ($el->required ? '&nbsp;<em>*</em>' : '');
	} else {
		echo '&nbsp;';
	}
	$inline = $el->inline;
	if (false === $inline) {
		$columns++;
		echo '</th>';
	}
}
echo '</tr></thead><tbody>';


foreach ($form->rows as $row) {
	render($row, $form);
}

echo '</tbody><tfoot>';

foreach ($form->header->elements as $el) {
	if ($el instanceof System_Form_Field_Submit) {
		echo  '<tr><td colspan="' . $columns . '"><div class="autoform-element">';

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
		echo '</div></td></tr>';
	}
}

echo "</tfoot></table></form>\n";

/* Local Variables:    */
/* tab-width: 4	       */
/* indent-tabs-mode: t */
/* End:                */
