<?php

/**
 * Kit.
 *
 * Form library.
 *
 * @version $Id: form.php 108 2010-07-29 04:18:38Z eprev $
 * @package System
 */
abstract class System_Form_Field {

	/**
	 * @var System_Form_Fieldset Onwer.
	 */
	public $fieldset;

	/**
	 * @var string Name.
	 */
	public $name;

	/**
	 * @var string Label.
	 */
	public $label;

	/**
	 * @var string Text align (used in batch).
	 */
	public $textAlign;

	/**
	 * @var boolean Whether this field is detatched (used in batch)?
	 */
	public $detatched = false;

	/**
	 * @var string Field width (used in batch).
	 */
	public $width;

	/**
	 * @var string Value.
	 */
	public $value;

	/**
	 * @var mixed Transformed value after validation.
	 */
	public $transValue;

	/**
	 * @var string Hint.
	 */
	public $hint;

	/**
	 * @var array Attributes.
	 */
	public $attributes = array();

	/**
	 * @var boolean Whether to group field with the previous one?
	 */
	public $grouped = false;

	/**
	 * @var boolean Whether this field is a phantom field?
	 */
	public $phantom = false;

	/**
	 * @var boolean Whether this field is disabled?
	 */
	public $disabled = false;

	/**
	 * @var boolean Whether to interpret empty value as null?
	 */
	public $nullable = false;

	/**
	 * @var boolean Whether field is visible (FALSE to hide)?
	 */
	public $visible = null;

	/**
	 * @var array Validation errors.
	 */
	public $errors;

	/**
	 * @var boolean Whether this field is required?
	 */
	public $required = false;

	/**
	 * @var array Validation rules.
	 */
	public $rules;

	/**
	 * @var array Pre-validation filters.
	 */
	public $beforeRules;

	/**
	 * @var array Post-validation filters.
	 */
	public $afterRules;

	/**
	 * @var boolean Whether field is modified?
	 */
	public $modified = false;

	/**
	 * @var string Whether this field is in one line with the next one.
	 */
	public $inline = false;

	/**
	 * @var string Content left.
	 */
	public $contentLeft;

	/**
	 * @var string Content top.
	 */
	public $contentTop;

	/**
	 * @var boolean Set multiple select
	 */
	public $gpouped = false;

	/**
	 * @var string Content right.
	 */
	public $contentRight;

	/**
	 * @var string Content bottom.
	 */
	public $contentBottom;

	/**
	 * @var array Rules.
	 *
	 * Array of ruleName => array(numberOfRuleParameters, errorMessage)
	 */
	public $ruleConfig = array(
		'required' => array(0, 'This field is required'),
		'minLength' => array(1, 'The minimum length for this field is {0}'),
		'maxLength' => array(1, 'The maximum length for this field is {0}'),
		'regex' => array(1, 'Value does not match pattern {0}'),
		'alias' => array(0, 'Only letters, numbers, "-" and "." are allowed'),
		'minValue' => array(1, 'The minimum value for this field is {0}'),
		'maxValue' => array(1, 'The maximum value for this field is {0}'),
		'integer' => array(0, 'This is not a valid integer'),
		'float' => array(0, 'This is not a valid decimal number'),
		'email' => array(0, 'This is not a valid e-mail address'),
		'url' => array(0, 'This is not a valid URL address'),
		'ip' => array(0, 'This is not a valid IP address'),
		'date' => array(0, 'Date must be in the format "YYYY-MM-DD"'),
		'datetime' => array(0, 'Date and time must be in the format "YYYY-MM-DD HH:II"'),
		'positive' => array(0, 'This is not a positive number'),
		'key' => array(0, 'Key not found'),
	);

	/**
	 * Constructor.
	 *
	 * @param System_Form_Fieldset $fieldset Owner.
	 * @param string               $name     Name.
	 * @param string               $label    Label.
	 * @param string               $value    Value.
	 * @return this
	 * @throws InvalidArgumentException
	 */
	public function __construct(System_Form_Fieldset $fieldset, $name, $label, $value) {
		$this->fieldset = $fieldset;
		$this->name = $name;
		$this->label = $label;
		$this->value = $value;
	}

	/**
	 * Helper for render();
	 */
	public function composeName() {
		if (preg_match('/^(.*)\[(.*)\]\[(.*)\]$/u', $this->name, $matches)) {
			return $this->fieldset->form->attributes['id'] . '[' . $matches[1] . '][' . $matches[2] . '][' . $matches[3] . ']';
		} elseif (preg_match('/^(.*)\[(.*)\]$/u', $this->name, $matches)) {
			return $this->fieldset->form->attributes['id'] . '[' . $matches[1] . '][' . $matches[2] . ']';
		} else {
			return $this->fieldset->form->attributes['id'] . '[' . $this->name . ']';
		}
	}

	/**
	 * Helper for render();
	 */
	public function composeId() {
		if (preg_match('/^(.*)\[(.*)\]\[(.*)\]$/u', $this->name, $matches)) {
			return $this->fieldset->form->attributes['id'] . '-' . $matches[1] . '-' . $matches[2] . '-' . $matches[3];
		} elseif (preg_match('/^(.*)\[(.*)\]$/u', $this->name, $matches)) {
			return $this->fieldset->form->attributes['id'] . '-' . $matches[1] . '-' . $matches[2];
		} else {
			return $this->fieldset->form->attributes['id'] . '-' . $this->name;
		}
	}

	/**
	 * Sets $hint value.
	 *
	 * @param string $value Hint.
	 * @return this
	 */
	public function hint($value) {
		$this->hint = $value;
		return $this;
	}

	/**
	 * Sets $required to true.
	 *
	 * @param boolean $value Is the filed required?
	 * @return this
	 */
	public function required($value = true) {
		$this->required = $value;
		return $this;
	}

	/**
	 * Sets $grouped value.
	 *
	 * @param boolean $value Is this the grouped field?
	 * @return this
	 */
	public function grouped($value = true) {
		$this->grouped = $value;
		return $this;
	}

	/**
	 * Sets $phantom value.
	 *
	 * @param boolean $value Is this the phantom field?
	 * @return this
	 */
	public function phantom($value = true) {
		$this->phantom = $value;
		return $this;
	}

	/**
	 * Sets $visible value.
	 *
	 * @param boolean $value Is this field visible?
	 * @return this
	 */
	public function visible($value = true) {
		$this->visible = $value;
		return $this;
	}

	/**
	 * Sets $width value.
	 *
	 * @param integer $value Label width.
	 * @return this
	 */
	public function width($value) {
		$this->width = $value;
		return $this;
	}

	/**
	 * Sets $detatched value.
	 *
	 * @param boolean $detatched Is this field detached?
	 * @return this
	 */
	public function detatched($value = true) {
		$this->detatched = $value;
		return $this;
	}

	/**
	 * Sets $textAlign value.
	 *
	 * @param string $value Text align.
	 * @return this
	 */
	public function textAlign($value) {
		$this->textAlign = $value;
		return $this;
	}

	/**
	 * Sets $disabled value.
	 *
	 * @param boolean $value Is this field disabled?
	 * @return this
	 */
	public function disabled($value = true) {
		$this->disabled = $value;
		return $this;
	}

	/**
	 * Sets $contentLeft value.
	 *
	 * @param string $value Content.
	 * @return this
	 */
	public function contentLeft($value) {
		$this->contentLeft = $value;
		return $this;
	}

	/**
	 * Sets $contentTop value.
	 *
	 * @param string $value Content.
	 * @return this
	 */
	public function contentTop($value) {
		$this->contentTop = $value;
		return $this;
	}

	/**
	 * Sets $contentRight value.
	 *
	 * @param string $value Content.
	 * @return this
	 */
	public function contentRight($value) {
		$this->contentRight = $value;
		return $this;
	}
	
	/**
	* Sets $bunched value.
	*
	* @param boolean $value  Is this the bunched field?
	* @return this
	*/
	public function bunched($value = true) {
	    $this->bunched = $value;
	    return $this;
	}

	/**
	 * Sets $contentBottom value.
	 *
	 * @param string $value Content.
	 * @return this
	 */
	public function contentBottom($value) {
		$this->contentBottom = $value;
		return $this;
	}

	/**
	 * Sets $inline value.
	 *
	 * @param boolean $value Is inline?
	 * @return this
	 */
	public function inline($value = true) {
		$this->inline = $value;
		return $this;
	}

	/**
	 * Sets $nullable value.
	 *
	 * @param boolean $value Is nullable?
	 * @return this
	 */
	public function nullable($value = true) {
		$this->nullable = $value;
		return $this;
	}

	/**
	 * Adds pre-validation filter to the element.
	 *
	 * @param function $handler Filter handler.
	 * @return this
	 */
	public function before($handler) {
		$this->beforeRules[] = $handler;
		return $this;
	}

	/**
	 * Adds post-validation filter to the element.
	 *
	 * @param function $handler Filter handler.
	 * @return this
	 */
	public function after($handler) {
		$this->afterRules[] = $handler;
		return $this;
	}

	/**
	 * Adds element attribute.
	 *
	 * @param string $name  Attribute name.
	 * @param string $value Attribute value.
	 * @return this
	 */
	public function attribute($name, $value = null) {
		if ($value === false and isset($this->attributes[$name])) {
			unset($this->attributes[$name]);
		} else {
			$this->attributes[$name] = (null === $value) ? $name : $value;
		}

		return $this;
	}

	/**
	 * Adds callback to the element.
	 *
	 * @param function $handler  Callback handler.
	 * @return this
	 */
	public function callback($handler, $args = array()) {
		$this->rules[] = array('callback', $handler);
		return $this;
	}

	/**
	 * Adds validation rule to element.
	 *
	 * @param string $type  Rule type.
	 * @return this
	 */
	public function rule($type) {
		if (is_string($type)) {
			if (false == array_key_exists($type, $this->ruleConfig)) {
				throw new InvalidArgumentException('Unsupported rule "' . $type . '".');
			}
			$ruleConfig = & $this->ruleConfig[$type];

			$args = func_get_args();
			$argc = count($args);

			if (($argc < $ruleConfig[0] + 1) || ($argc > $ruleConfig[0] + 2)) {
				throw new InvalidArgumentException('Invalid number of arguments for rule "' . $type . '".');
			}

			// Get rule parameters
			if ($ruleConfig[0] == 1) {
				$value = $args[1];
			} elseif ($ruleConfig[0] > 1) {
				$value = array_slice($args, 1, $ruleConfig[0]);
			} else {
				$value = null;
				if ('alias' == $type) {
					$value = '/^[a-z0-9.-]+$/us';
				}
			}

			// Get custom error text
			if ($argc == $ruleConfig[0] + 2) { // ($type, $param0, ..., $paramN, $text)
				$ruleConfig[1] = $args[$argc - 1];
			}

			if ('required' == $type) {
				$this->required = true;
			}
			if ('maxLength' == $type) {
				$this->attribute('maxLength', $value);
			}
			$this->rules[$type] = array($type, $value);
		} else {
			$this->rules[] = array('callback', $type);
		}
		return $this;
	}

	/**
	 * Validate value according to set rules.
	 *
	 * @return boolean
	 */
	public function validate() {
		if ($this->beforeRules) {
			foreach ($this->beforeRules as $handler) {
				call_user_func($handler, $this);
			}
		}
		$this->errors = array();
		if (('' === $this->value || $this->value === null) && $this->required) {
			$this->errors[] = $this->ruleConfig['required'][1];
		}
		if ('' !== $this->value && $this->rules) {
			$number = false;
			foreach ($this->rules as $rule) {
				list($name, $value) = $rule;
				switch ($name) {
					case 'minLength':
						if (mb_strlen($this->value, 'utf-8') < $value) {
							$this->errors[] = Text::format($this->ruleConfig[$name][1], $value);
						}
						break;
					case 'maxLength':
						if (mb_strlen($this->value, 'utf-8') > $value) {
							$this->errors[] = Text::format($this->ruleConfig[$name][1], $value);
						}
						break;
					case 'regex':
					case 'alias':
						if (false == (bool) preg_match($value, $this->value)) {
							$this->errors[] = Text::format($this->ruleConfig[$name][1], $value);
						}
						break;
					case 'integer':
						// Use regexp because of leading zeros and long numbers
						if (0 == preg_match('/^\d+$/', $this->value, $matches)) {
							$this->errors[] = $this->ruleConfig[$name][1];
						} else {
							$number = intval($this->value);
						}
						break;
					case 'float':
						if (false === ($number = filter_var($this->value, FILTER_VALIDATE_FLOAT))) {
							$this->errors[] = $this->ruleConfig[$name][1];
						}
						break;
					case 'positive':
						if (!empty($this->value) && $this->value < 0) {
							$this->errors[] = $this->ruleConfig[$name][1];
						}
						break;
					case 'minValue':
						$number = intval($this->value);
						if (false !== $number && $number < $value) {
							$this->errors[] = Text::format($this->ruleConfig[$name][1], $value);
						}
						break;
					case 'maxValue':
						$number = intval($this->value);
						if (false !== $number && $number > $value) {
							$this->errors[] = Text::format($this->ruleConfig[$name][1], $value);
						}
						break;
					case 'ip':
						if (false === filter_var($this->value, FILTER_VALIDATE_IP)) {
							$this->errors[] = $this->ruleConfig[$name][1];
						}
						break;
					case 'email':
						if (false === filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
							$this->errors[] = $this->ruleConfig[$name][1];
						}
						break;
					case 'url':
						if(substr($this->value, 0, 4) !== 'http') {
							$this->value = 'http://' . $this->value;
						}
						if (false === filter_var($this->value, FILTER_VALIDATE_URL)) {
							$this->errors[] = $this->ruleConfig[$name][1];
						}
						break;
					case 'date':
						if (false == (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/us', $this->value)) {
							$this->errors[] = $this->ruleConfig[$name][1];
						}
						break;
					case 'datetime':
						if (false == (bool) preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/us', $this->value)) {
							$this->errors[] = $this->ruleConfig[$name][1];
						}
						break;
					case 'callback':
						$message = call_user_func($value, $this);
						if (is_string($message)) {
							$this->errors[] = $message;
						}
						break;
				}
			}
		}
		if (0 == count($this->errors) && $this->afterRules) {
			foreach ($this->afterRules as $handler) {
				call_user_func($handler, $this);
			}
		}
		return 0 == count($this->errors);
	}

	/**
	 * Adds custom error while validating.
	 *
	 * @param string $message  Error message.
	 * @return this
	 */
	public function error($message) {
		$this->errors[] = $message;
		return $this;
	}

	/**
	 * Sets a new value of the field (marks as modified).
	 *
	 * @param  string  $value  2New value of the field.
	 * @return this
	 */
	public function setValue($value) {
		if ($this->value != $value) {
			$this->value = $value;
			$this->modified = true;
		}
		return $this;
	}

	/**
	 * Retursn the value.
	 *
	 * @return mixed
	 */
	public function getValue() {
		$value = null !== $this->transValue ? $this->transValue : $this->value;
		return ('' == $value && $this->nullable) ? null : $value;
	}

	/**
	 * Renders HTML.
	 *
	 * @return string
	 */
	abstract public function render();
}

class System_Form_Field_Hidden extends System_Form_Field {

	/**
	 * Constructor.
	 *
	 * @param System_Form_Fieldset $fieldset Owner.
	 * @param string          $name     Name.
	 * @param string          $value    Value.
	 * @return this
	 * @throws InvalidArgumentException
	 */
	public function __construct(System_Form_Fieldset $fieldset, $name, $value) {
		parent::__construct($fieldset, $name, null, $value);
	}

	public function render() {
		return '<input' . Html::attributes(array_merge($this->attributes, array(
			    'type' => 'hidden',
			    'id' => $this->composeId(),
			    'name' => $this->composeName(),
			    'value' => $this->value
			))) . ' />';
	}

}


class System_Form_Field_Text extends System_Form_Field {

	public function render() {
		return '<input' . Html::attributes(array_merge($this->attributes, array(
			    'type' => 'text',
			    'id' => $this->composeId(),
			    'name' => $this->composeName(),
			    'value' => $this->value
			))) . ' />';
	}

}

class System_Form_Field_Number extends System_Form_Field {

	public function render() {
		return '<input' . Html::attributes(array_merge($this->attributes, array(
			'type' => 'number',
			'id' => $this->composeId(),
			'name' => $this->composeName(),
			'value' => $this->value
		))) . ' />';
	}

}

class System_Form_Field_Html extends System_Form_Field {

	public function render() {
		return '<span' . Html::attributes(array_merge($this->attributes, array(
			    'id' => $this->composeId()
			))) . '>' . $this->value . '</span>';
	}

}

class System_Form_Field_Password extends System_Form_Field {

	public function render() {
		return '<input' . Html::attributes(array_merge($this->attributes, array(
			    'type' => 'password',
			    'id' => $this->composeId(),
			    'name' => $this->composeName(),
			    'value' => $this->value,
			    'autocomplete' => 'off'
			))) . ' />';
	}

}

class System_Form_Field_Select extends System_Form_Field {

	/**
	 * @var array Element options.
	 */
	public $options;

	/**
	 * @var array disabled options.
	 */
	public $optDisabled;

	/**
	 * @var boolean Set multiple select
	 */
	public $multiple = false;

	/**
	 * Set multiple select.
	 *
	 * @param boolean $value  Is this the bunched field?
	 * @return this
	 */
	public function multiple($value = true) {
		$this->multiple = $value;
		return $this;
	}

	/**
	 * Constructor.
	 *
	 * @param System_Form_Fieldset $fieldset Owner.
	 * @param string          $name     Name.
	 * @param array           $name     Options.
	 * @param string          $label    Label.
	 * @param string          $value    Value.
	 * @return this
	 */
	public function __construct(System_Form_Fieldset $fieldset, $name, array $options, $label = null, $value = null, array $disabled = null) {
		parent::__construct($fieldset, $name, $label, $value);
		$this->options = $options;
		$this->optDisabled = $disabled;
		$this->multiple = false;
	}

	/**
	 * Validate value according to set rules.
	 *
	 * @return boolean
	 */
	public function validate()
	{
		$this->errors = array();

		if($this->multiple && is_array($this->value)) {
			if(array_diff($this->value, $this->options)) {
				$this->errors[] = $this->ruleConfig['key'][1];
			}
		} else {
			if(!array_key_exists($this->value, $this->options)) {
				$this->errors[] = $this->ruleConfig['key'][1];
			}
		}

		parent::validate();

		return 0 == count($this->errors);
	}

	public function render() {
		$options = array(
		    'id' => $this->composeId(),
		    'name' => $this->composeName()
		);
		if ($this->multiple) {
			$options['multiple'] = 'multiple';
			$options['name'] = $options['name'] . '[]';
			$this->value = strlen($this->value) > 0 ? explode('#', trim($this->value, '#')) : null;
		}

		$result = '<select' . Html::attributes(array_merge($this->attributes, $options)) . ">\n";

		foreach ($this->options as $key => $value) {
			if (is_array($value)) {
				if (!empty($value['options'])) {
					$result .= '<optgroup label="' . (isset($value['label']) ? $value['label'] : 'Group') . '" >';
					foreach ($value['options'] as $k => $option) {
						if (is_array($this->value)) {
							$selected = in_array($k, $this->value);
						} else {
							$selected = ($this->value && $this->value == $k);
						}

						$result .= '<option value="' . Html::chars($k) . '"'
							. ($selected ? ' selected="selected"' : '')
							. ((!empty($this->optDisabled) && in_array($k, $this->optDisabled, true)) ? ' disabled="disabled"' : '')
							. '>' . $option . "</option>";
					}
					$result .= '</optgroup>';
				}
			} else {
				if (is_array($this->value)) {
					$selected = in_array($key, $this->value);
				} else {
					$selected = ($this->value && $this->value == $key);
				}

				$result .= chr(9) . '<option value="' . Html::chars($key) . '"'
					. ($selected ? ' selected="selected"' : '')
					. ((!empty($this->optDisabled) && in_array($key, $this->optDisabled, true)) ? ' disabled="disabled"' : '')
					. '>' . $value . "</option>\n";
			}
		}
		return $result . '</select>';
	}

}

class System_Form_Field_Radio extends System_Form_Field {

	/**
	 * @var array  Options.
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @param System_Form_Fieldset $fieldset Owner.
	 * @param string          $name     Name.
	 * @param array           $name     Options.
	 * @param string          $label    Label.
	 * @param string          $value    Value.
	 * @return this
	 */
	public function __construct(System_Form_Fieldset $fieldset, $name, $options, $label = null, $value = null) {
		parent::__construct($fieldset, $name, $label, $value);
		$this->options = $options;
	}

	public function render() {
		$id = $this->composeId() . '-';
		$result = '';
		foreach ($this->options as $key => $value) {
			$result .= '<span class="form-radio-option"><input' . Html::attributes(array_merge($this->attributes, array(
				    'type' => 'radio',
				    'id' => $id . $key,
				    'name' => $this->composeName(),
				    'value' => $key,
				))) . ($this->value == $key ? ' checked="checked"' : '') . ' />&nbsp;<label for="' . $id . $key . '">' . $value . '</label></span>';
		}
		return $result;
	}

}

class System_Form_Field_Checkbox extends System_Form_Field {

	/**
	 * @var string  Checkbox label.
	 */
	protected $boxLabel;

	/**
	 * Constructor.
	 *
	 * @param System_Form_Fieldset $fieldset Owner.
	 * @param string          $name     Name.
	 * @param string          $label    Label.
	 * @param string          $boxLabel Checkbox label.
	 * @param string          $value    Value.
	 * @return this
	 */
	public function __construct(System_Form_Fieldset $fieldset, $name, $label = null, $boxLabel = null, $value = null) {
		parent::__construct($fieldset, $name, $label, $value);
		$this->boxLabel = $boxLabel;
	}

	public function render() {
		$id = $this->composeId();
		$html = '<input' . Html::attributes(array_merge($this->attributes, array(
			    'type' => 'checkbox',
			    'id' => $id,
			    'name' => $this->composeName(),
			))) . ' value="'. ($this->value ? '1' : '0') .'" />';
		if ($this->boxLabel) {
			$html .= '&nbsp;<label for="' . $id . '">' . $this->boxLabel . '</label>';
		}
		return $html;
	}

}

class System_Form_Field_Textarea extends System_Form_Field {

	public function render() {
		return '<textarea' . Html::attributes(array_merge($this->attributes, array(
			    'id' => $this->composeId(),
			    'name' => $this->composeName(),
			))) . '>' . Html::chars($this->value) . '</textarea>';
	}

}

class System_Form_Field_Submit extends System_Form_Field {

	/**
	 * @var array  Alternative link (eg. [Submit] or `Cancel`) - array of arguments to Html::anchor().
	 */
	public $altLink;

	/**
	 * Constructor.
	 *
	 * @param System_Form_Fieldset $fieldset Owner.
	 * @param string          $name     Name.
	 * @param string          $value    Value.
	 * @param array           $altLink  Alternative Link.
	 * @return this
	 */
	public function __construct(System_Form_Fieldset $fieldset, $name, $value = null, $altLink = null) {
		parent::__construct($fieldset, $name, null, $value);
		$this->altLink = $altLink;
	}

	public function render() {
		$html = '<input' . Html::attributes(array_merge($this->attributes, array(
			    'type' => 'submit',
			    'id' => $this->composeId(),
			    'name' => $this->composeName(),
			    'value' => $this->value
			))) . ' />';
		if ($this->altLink) {
			$html .= '&nbsp;or&nbsp;' . call_user_func_array(array('html', 'anchor'), $this->altLink);
		}
		return $html;
	}

}

class System_Form_Field_File extends System_Form_Field {

	/**
	 * @var array Uploaded files.
	 */
	public $files = null;
	public $callbacks = null;

	/**
	 * @var array Rules.
	 *
	 * Array of ruleName => array(numberOfRuleParameters, errorMessage)
	 */
	public $ruleConfig = array(
	    'required' => array(0, 'This field is required'),
	    'mime' => array(1, 'File "{0}" is of {1} type, only {2} allowed'),
	    'extension' => array(1, 'File "{0}" has restricted extension, only {2} allowed')
	);

	/**
	 * Constructor.
	 *
	 * @param System_Form_Fieldset $fieldset Owner.
	 * @param string          $name     Name.
	 * @param string          $label    Label.
	 * @return this
	 */
	public function __construct(System_Form_Fieldset $fieldset, $name, $label = null) {
		parent::__construct($fieldset, $name, $label, null);
		$this->fieldset->form->attributes['enctype'] = 'multipart/form-data';
	}

	/**
	 * Returns upload error message by its number.
	 *
	 * @param integer $errorNo  Error number.
	 * @return string
	 */
	public static function getUploadErrorMessage($errorNo) {
		switch ($errorNo) {
			case UPLOAD_ERR_INI_SIZE:
				return 'File "{0}" exceeds the upload_max_filesize directive.';
			case UPLOAD_ERR_FORM_SIZE:
				return 'File "{0}" exceeds the MAX_FILE_SIZE directive.';
			case UPLOAD_ERR_PARTIAL:
				return 'File "{0}" was only partially uploaded.';
			case UPLOAD_ERR_NO_FILE:
				return 'No file was uploaded.';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'Missing a temporary folder.';
			case UPLOAD_ERR_CANT_WRITE:
				return 'Failed to write file "{0}" to disk.';
			case UPLOAD_ERR_EXTENSION:
				return 'File upload stopped by extension.';
			default:
				return 'Don\'t really know what\'s up with "{0}".';
		}
	}

	/**
	 * Validate value according to set rules.
	 *
	 * @return boolean
	 */
	public function validate() {
		if ($this->beforeRules) {
			foreach ($this->beforeRules as $handler) {
				call_user_func($handler, $this);
			}
		}

		$this->errors = array();
		if (array_key_exists($this->name, $_FILES)) {
			$files = array();

			// Collect uploaded files without errors
			for ($i = 0, $c = count($_FILES[$this->name]['error']); $i < $c; $i++) {
				if ($name = $_FILES[$this->name]['name'][$i]) {
					$error = $_FILES[$this->name]['error'][$i];
					if (0 == $error) {
						$files[] = array(
						    'key' => $i,
						    'name' => $name,
						    'type' => $_FILES[$this->name]['type'][$i], //File::mime($_FILES[$this->name]['tmp_name'][$i]),
						    'extension' => strtolower(File::extension($_FILES[$this->name]['name'][$i])),
						    'tmp_name' => $_FILES[$this->name]['tmp_name'][$i],
						    'size' => $_FILES[$this->name]['size'][$i],
						);
					} else {
						$this->errors[] = Text::format(self::getUploadErrorMessage($error), $name);
					}
				}
			}

			// Filter files by rules
			if ($this->rules) {
				foreach ($this->rules as $rule) {
					list($name, $value) = $rule;
					if ('mime' == $name || 'extension' == $name) {
						$value = (array) $value;
						foreach ($files as $i => $file) {
							switch ($name) {
								case 'extension':
									$validationArray = $file['extension'];
									break;
								case 'mime':
									$validationArray = $file['type'];
									break;
							}
							if (false === in_array($validationArray, $value)) {
								$this->errors[] = Text::format(
										$this->ruleConfig[$name][1], $file['name'], $file['type'], implode(', ', $value)
								);
								unset($files[$i]);
							}
						}
					} elseif ('callback' == $name) {
						$this->callbacks[] = array('value' => $value, 'this' => $this);

//						$message = call_user_func($value, $this);
//						if (is_string($message)) {
//							$this->errors[] = array('value' => $value, 'this' => $this);
//						}
					}
				}
			}

			if (count($files) > 0) {
				$this->files = array_values($files);
			}
		}

		if (null === $this->files && $this->required) {
			$this->errors[] = $this->ruleConfig['required'][1];
		}

		if (0 == count($this->errors) && $this->callbacks) {
			foreach ($this->callbacks as $callback) {
				$message = call_user_func($callback['value'], $callback['this']);
				if (is_string($message)) {
					$this->errors[] = $message;
				}
			}
		}

		if (0 == count($this->errors) && $this->afterRules) {
			foreach ($this->afterRules as $handler) {
				call_user_func($handler, $this);
			}
		}
		return 0 == count($this->errors);
	}

	public function render() {
		return '<input' . Html::attributes(array_merge($this->attributes, array(
			    'type' => 'file',
			    'id' => $this->composeId(),
			    'name' => $this->name . '[]'
			))) . ' />';
	}

}

class System_Form_Fieldset {

	/**
	 * @var Form Owner;
	 */
	public $form;

	/**
	 * @var string Name.
	 */
	public $name;

	/**
	 * @var array Attributes.
	 */
	public $attributes;

	/**
	 * @var string Legend.
	 */
	public $legend;

	/**
	 * @var array User errors.
	 */
	public $errors;

	/**
	 * @var boolean Whether to display errors above the fieldset or below the elements.
	 */
	public $combineErrors;

	/**
	 * @var array Elements.
	 */
	public $elements = array();

	/**
	 * @var boolean Whether this fieldset is disabled?
	 */
	public $disabled = false;

	/**
	 * @var mixed Width of labels.
	 */
	public $labelWidth;

	/**
	 * Constructor.
	 *
	 * @param System_Form_Interface $form Owner.
	 * @param string $id      Form Id.
	 * @param string $legend  Legend.
	 * @param string $action  Submit URL.
	 * @return this
	 */
	public function __construct(System_Form_Interface $form, $name, $legend = null, $attributes = array()) {
		$this->form = $form;
		$this->name = $name;
		$this->legend = $legend;
		$this->attributes = $attributes;
	}

	/**
	 * Sets $disabled value.
	 *
	 * @param boolean $value Is this field disabled?
	 * @return this
	 */
	public function disabled($value = true) {
		$this->disabled = $value;
		return $this;
	}

	/**
	 * Adds fieldset attribute.
	 *
	 * @param string $name  Attribute name.
	 * @param string $value Attribute value.
	 * @return this
	 */
	public function attribute($name, $value = null) {
		$this->attributes[$name] = (null === $value) ? $name : $value;
		return $this;
	}

	/**
	 * Sets $legend value.
	 *
	 * @param string $value Legend.
	 * @return this
	 */
	public function legend($value) {
		$this->legend = $value;
		return $this;
	}

	/**
	 * Adds custom error while validating.
	 *
	 * @param string $message  Error message.
	 * @return this
	 */
	public function error($message) {
		$this->errors[] = $message;
		return $this;
	}

}

interface System_Form_Interface {
	
}

class System_Form implements System_Form_Interface {

	/**
	 * @var string Form template.
	 */
	public $template = 'form';

	/**
	 * @var array Attributes.
	 */
	public $attributes = array();

	/**
	 * @var mixed  Width of labels.
	 */
	public $labelWidth;

	/**
	 * @var mixed Label separator.
	 */
	public $labelSeparator = '';

	/**
	 * @var array Elements.
	 */
	public $elements = array();

	/**
	 * @var System_Form_Fieldset Current fieldset.
	 */
	public $fieldset;

	/**
	 * @var array Fieldsets.
	 */
	public $fieldsets = array();

	/**
	 * @var boolean Whether to display errors above the fieldsets or below the elements.
	 */
	public $combineErrors = false;

	/**
	 * @var string HTML for hints.
	 */
	public $hintHtml = '?';

	/**
	 * Constructor.
	 *
	 * @param string $id      Form Id.
	 * @param string $legend  Default fieldset's legend.
	 * @param string $action  Submit URL.
	 * @return this
	 */
	public function __construct($id, $legend = '', $action = '') {
		$this->attributes['action'] = $action;
		$this->attributes['class'] = 'autoform';
		$this->attributes['id'] = $id;
		$this->attributes['method'] = 'post';
		$this->fieldsets['default'] = $this->fieldset = new System_Form_Fieldset($this, 'default', $legend);
	}

	/**
	 * Adds form attribute.
	 *
	 * @param string $name  Attribute name.
	 * @param string $value Attribute value.
	 * @return this
	 */
	public function attribute($name, $value = null) {
		$this->attributes[$name] = (null === $value) ? $name : $value;
		return $this;
	}

	/**
	 * Switches the current fieldset or creates a new one.
	 *
	 * @param string $name        Fieldset name.
	 * @param string $legend      Fieldset legend.
	 * @param string $attributes  Fieldset attributes.
	 * @return System_Form_Fieldset
	 */
	public function fieldset($name = 'default', $legend = null, $attributes = null) {
		if (array_key_exists($name, $this->fieldsets)) {
			$this->fieldset = $this->fieldsets[$name];
		} else {
			$this->fieldsets[$name] = $this->fieldset = new System_Form_Fieldset($this, $name, $legend, $attributes ? : array());
		}
		return $this->fieldset;
	}

	/**
	 * Validates the form.
	 *
	 * @return boolean
	 */
	public function validate() {
		$valid = true;
		foreach ($this->fieldsets as $fs) {
			$fs->errors = array();
		}
		if (count($this->elements) <= 1 && !empty($this->elements['submit'])) {
			return true;
		}
		if (isset($_POST[$this->attributes['id']]) || isset($_GET[$this->attributes['id']])) {
			if(isset($_POST[$this->attributes['id']])) {
				$post = $_POST[$this->attributes['id']];
			} else {
				$post = $_GET[$this->attributes['id']];
			}
			foreach ($this->elements as $el) {
				if ($el instanceof System_Form_Field_Submit || $el instanceof System_Form_Field_Html) {
					continue;
				}

				$index = null;
				$index2 = null;
				if (preg_match('/^(.*)\[(.*)\]\[(.*)\]$/u', $el->name, $matches)) {
					$name = $matches[1];
					if (isset($matches[2])) {
						$index = $matches[2];
					}
					if (isset($matches[3])) {
						$index2 = $matches[3];
					}
				} elseif (preg_match('/^(.*)\[(.*)\]$/u', $el->name, $matches)) {
					$name = $matches[1];
					if (!empty($matches[2])) {
						$index = $matches[2];
					}
				} else {
					$name = $el->name;
				}

				if ($el instanceof System_Form_Field_Checkbox) {
					$value = array_key_exists($name, $post);
					if (null !== $index) {
						$value = array_key_exists($index, $value);
					}
				} elseif ($el instanceof System_Form_Field_Select && array_key_exists($name, $post) && $el->multiple) {
					$value = (!empty($post[$name])) ? '#' . implode('#', $post[$name]) . '#' : null;
				} elseif (array_key_exists($name, $post)) {
					$value = $post[$name];
					if (null !== $index) {
						$value = array_key_exists($index, $value) ? $value[$index] : null;
						if (null !== $index2 && is_array($value)) {
							$value = array_key_exists($index2, $value) ? $value[$index2] : null;
						}
					}
				} else {
					$value = null;
				}
				$el->modified = $el->value != $value;
				$el->value = $value;
			}
			foreach ($this->elements as $el) {
				if (
					$el->disabled || $el->fieldset->disabled || $el instanceof System_Form_Field_Submit
//					|| $el instanceof System_Form_Field_Html
				) {
					continue;
				}
				$valid &= $el->validate();
			}
		} else {
			$valid = false;
		}
		return $valid;
	}

	/**
	 * Loads elements' values.
	 *
	 * @param array $values  Values.
	 * @return this
	 */
	public function loadValues($values) {
		
		foreach ($values as $key => $value) {
			
			if (array_key_exists($key, $this->elements)) {
				$this->elements[$key]->value = $value;
				
			}
		}
		
		return $this;
	}

	/**
	 * Returns elements values.
	 *
	 * @return array
	 */
	public function getValues() {
		$data = array();
		foreach ($this->elements as $el) {
			if (
				$el->disabled || $el->fieldset->disabled || $el instanceof System_Form_Field_Submit || $el instanceof System_Form_Field_File || $el instanceof System_Form_Field_Html || $el->phantom
			) {
				continue;
			}
			if (preg_match('/^(.*)\[(.*)\]\[(.*)\]$/u', $el->name, $matches)) {
				$name = $matches[1];
			} elseif (preg_match('/^(.*)\[(.*)\]$/u', $el->name, $matches)) {
				$name = $matches[1];
			} else {
				$name = $el->name;
			}
			$data[$name] = $el->getValue();
		}
		return $data;
	}

	/**
	 * Returns only modified elements values.
	 *
	 * @return array
	 */
	public function getModified() {
		$data = array();
		foreach ($this->elements as $el) {
			if (
				$el->disabled || $el->fieldset->disabled || $el instanceof System_Form_Field_Submit || $el instanceof System_Form_Field_File || $el instanceof System_Form_Field_Html || $el->modified == false || $el->phantom
			) {
				continue;
			}
			if (preg_match('/^(.*)\[(.*)\]\[(.*)\]$/u', $el->name, $matches)) {
				$name = $matches[1];
			}
			if (preg_match('/^(.*)\[(.*)\]$/u', $el->name, $matches)) {
				$name = $matches[1];
			} else {
				$name = $el->name;
			}
			$data[$name] = $el->getValue();
		}
		return $data;
	}

	public function clearValues() {
		foreach ($this->elements as $el) {
			if (
				$el instanceof System_Form_Field_Submit || $el instanceof System_Form_Field_File || $el instanceof System_Form_Field_Html
			) {
				continue;
			}
			$el->value = null;
		}
	}

	/**
	 * PHP magic __toString().
	 *
	 * @return string
	 */
	public function __toString() {
		ob_start();
		try {
			$this->render();
		} catch (Exception $e) {
			ob_end_clean();
			return ucfirst($e);
		}
		return ob_get_clean();
	}

	/**
	 * Renders header of the form.
	 *
	 * @return void
	 */
	public function header() {
		View::factory($this->template, array('form' => $this, 'render' => '__header__'))->render();
	}

	/**
	 * Renders footer of the form.
	 *
	 * @return void
	 */
	public function footer() {
		View::factory($this->template, array('form' => $this, 'render' => '__footer__'))->render();
	}

	/**
	 * Outputs the form entirely or a paticular fieldset only.
	 *
	 * @param string $fieldset Fieldset name to render.
	 * @return void
	 * @throws InvalidArgumentException
	 */
	public function render($fieldset = null) {
		if ($fieldset) {
			if (array_key_exists($fieldset, $this->fieldsets)) {
				View::factory($this->template, array('form' => $this, 'render' => $fieldset))->render();
			} else {
				throw new InvalidArgumentException('Fieldset "' . $fieldset . '" does not exists.');
			}
		} else {
			echo View::factory($this->template, array('form' => $this));
		}
	}

	public function hidden($name, $value = null) {
		$el = new System_Form_Field_Hidden($this->fieldset, $name, $value);
		return $this->fieldset->elements[$name] = $this->elements[$name] = $el;
	}


	public function text($name, $label = null, $value = null) {
		$el = new System_Form_Field_Text($this->fieldset, $name, $label, $value);
		return $this->fieldset->elements[$name] = $this->elements[$name] = $el;
	}

	public function number($name, $label = null, $value = null) {
		$el = new System_Form_Field_Number($this->fieldset, $name, $label, $value);
		return $this->fieldset->elements[$name] = $this->elements[$name] = $el;
	}

	public function password($name, $label = null, $value = null) {
		$el = new System_Form_Field_Password($this->fieldset, $name, $label, $value);
		return $this->fieldset->elements[$name] = $this->elements[$name] = $el;
	}

	public function select($name, $options, $label = null, $value = null, $disabled = null) {
		$el = new System_Form_Field_Select($this->fieldset, $name, $options, $label, $value, $disabled);
		return $this->fieldset->elements[$name] = $this->elements[$name] = $el;
	}

	public function radio($name, $options, $label = null, $value = null) {
		$el = new System_Form_Field_Radio($this->fieldset, $name, $options, $label, $value);
		return $this->fieldset->elements[$name] = $this->elements[$name] = $el;
	}

	public function checkbox($name, $label = null, $boxLabel = null, $value = null) {
		$el = new System_Form_Field_Checkbox($this->fieldset, $name, $label, $boxLabel, $value);
		return $this->fieldset->elements[$name] = $this->elements[$name] = $el;
	}

	public function textarea($name, $label = null, $value = null) {
		$el = new System_Form_Field_Textarea($this->fieldset, $name, $label, $value);
		return $this->fieldset->elements[$name] = $this->elements[$name] = $el;
	}

	public function html($name, $label = null, $value = null) {
		$el = new System_Form_Field_Html($this->fieldset, $name, $label, $value);
		return $this->fieldset->elements[$name] = $this->elements[$name] = $el;
	}

	public function file($name, $label = null) {
		$el = new System_Form_Field_File($this->fieldset, $name, $label);
		return $this->fieldset->elements[$name] = $this->elements[$name] = $el;
	}

	public function submit($name, $value, $altLink = null) {
		$el = new System_Form_Field_Submit($this->fieldset, $name, $value, $altLink);
		return $this->fieldset->elements[$name] = $this->elements[$name] = $el;
	}

}

//class Form_Batch implements Form_Interface
//{
//	/**
//	 * @var string Form template.
//	 */
//	public $template = 'form-batch';
//
//	/**
//	 * @var array Attributes.
//	 */
//	public $attributes = array();
//
//	/**
//	 * @var System_Form_Fieldset Header.
//	 */
//	public $header;
//
//	/**
//	 * @var array Array of fieldsets.
//	 */
//	public $rows = array();
//
//	/**
//	 * @var string HTML for hints.
//	 */
//	public $hintHtml = '?';
//
//	/**
//	 * Constructor.
//	 *
//	 * @param string $id      Form Id.
//	 * @param string $action  Submit URL.
//	 * @return this
//	 */
//	public function __construct($id, $action = '')
//	{
//		$this->attributes['action'] = $action;
//		$this->attributes['class']  = 'autoform';
//		$this->attributes['id']     = $id;
//		$this->attributes['method'] = 'post';
//		$this->header = new System_Form_Fieldset($this, 'header');
//	}
//
//	/**
//	 * Validates the form.
//	 *
//	 * @param boolean $loadValues Auto load values from query?
//	 * @return boolean
//	 */
//	public function validate($loadValues = false)
//	{
//		$valid = true;
//		if (isset($_POST[$this->attributes['id']])) {
//			$data = $_POST[$this->attributes['id']];
//
//			if ($loadValues) {
//				$count = -1;
//				foreach ($data as $value) {
//					if (is_array($value)) {
//						// find max key number
//						$count = array_reduce(array_keys($value), function ($v, $acc) {
//							return $v > $acc ? $v : $acc;
//						}, $count);
//					}
//				}
//				if ($count > -1) {
//					$rows = array_fill(0, $count + 1, array());
//					$this->loadValues($rows);
//				}
//			}
//
//			foreach ($this->header->elements as $el) {
//				if ($el instanceof Form_Field_Hidden && $el->detatched) {
//					if (array_key_exists($el->name, $data)) {
//						$value = $data[$el->name];
//					} else {
//						$value = null;
//					}
//					$el->modified = $el->value != $value;
//					$el->value = $value;
//				}
//			}
//
//			foreach ($this->rows as $id => $row) {
//				foreach ($row->elements as $el) {
//					if (
//						   $el instanceof Form_Field_Hidden
//						|| $el instanceof Form_Field_Submit
//						|| $el instanceof Form_Field_Html
//						|| $el->detatched
//					) {
//						continue;
//					}
//					$name = substr($el->name, 0, strpos($el->name, '['));
//					if (array_key_exists($name, $data)) {
//						if ($el instanceof Form_Field_Checkbox) {
//							$value = array_key_exists($id, $data[$name]);
//						} elseif (array_key_exists($id, $data[$name])) {
//							$value = $data[$name][$id];
//						} else {
//							$value = null;
//						}
//					} else {
//						$value = null;
//					}
//					$el->modified = $el->value != $value;
//					$el->value = $value;
//				}
//			}
//
//			foreach ($this->rows as $id => $row) {
//				foreach ($row->elements as $el) {
//					if (
//						   $row->disabled
//						|| $el->fieldset->disabled
//						|| $el instanceof Form_Field_Submit
//						|| $el instanceof Form_Field_Html
//					) {
//						continue;
//					}
//					$valid &= $el->validate();
//				}
//			}
//
//		} else {
//			$valid = false;
//		}
//		return $valid;
//	}
//
//	/**
//	 * Loads elements' values.
//	 *
//	 * @param array $rows Rows of values.
//	 * @return this
//	 */
//	public function loadValues($rows)
//	{
//		$id = count($this->rows);
//		foreach ($rows as $values) {
//			$row = new System_Form_Fieldset($this, $id);
//			foreach ($this->header->elements as $el) {
//				if (
//					   $el instanceof Form_Field_Submit
//					|| $el instanceof Form_Field_Html
//				) {
//					continue;
//				}
//				$el = clone $el;
//				if (array_key_exists($el->name, $values)) {
//					$el->value = $values[$el->name];
//				}
//				$el->fieldset = $row;
//				$row->elements[$el->name] = $el;
//				$el->name .= '[' . $id . ']';
//			}
//			$this->rows[$id] = $row;
//			$id++;
//		}
//		return $this;
//	}
//
//	/**
//	 * Returns elements values.
//	 *
//	 * @return array
//	 */
//	public function getValues()
//	{
//		$rows = array();
//		foreach ($this->rows as $id => $row) {
//			if (!empty($row->disabled)) continue;
//			$data = array();
//			foreach ($row->elements as $el) {
//				if (
//					   $el->disabled
//					|| $el->fieldset->disabled
//					|| $el instanceof Form_Field_Submit
//					|| $el instanceof Form_Field_Html
//					|| $el->phantom
//				) {
//					continue;
//				}
//				$name = substr($el->name, 0, strpos($el->name, '['));
//				$data[$name] = $el->getValue();
//			}
//			$rows[$id] = $data;
//		}
//		return $rows;
//	}
//
//	/**
//	 * Returns only modified rows values.
//	 *
//	 * @return array
//	 */
//	public function getModified()
//	{
//		$rows = array();
//		foreach ($this->rows as $id => $row) {
//			if (!empty($row->disabled)) continue;
//			$data = array();
//			foreach ($row->elements as $el) {
//				if (
//					   $el->disabled
//					|| $el->fieldset->disabled
//					|| $el instanceof Form_Field_Submit
//					|| $el instanceof Form_Field_Html
//					|| $el->modified == false
//				) {
//					continue;
//				}
//				$name = substr($el->name, 0, strpos($el->name, '['));
//				$data[$name] = $el->getValue();
//			}
//			$rows[$id] = $data;
//		}
//		return $rows;
//	}
//
//	public function clearValues()
//	{
//		foreach ($this->rows as $row) {
//			foreach ($row as $el) {
//				if (
//					   $el instanceof Form_Field_Submit
//					|| $el instanceof Form_Field_File
//					|| $el instanceof Form_Field_Html
//				) {
//					continue;
//				}
//				$el->value = null;
//			}
//		}
//	}
//
//    /**
//     * PHP magic __toString().
//     *
//     * @return string
//     */
//    public function __toString()
//    {
//		ob_start();
//		try {
//			$this->render();
//		} catch (Exception $e) {
//			ob_end_clean();
//			return ucfirst($e);
//		}
//		return ob_get_clean();
//    }
//
//	/**
//	 * Outputs the form entirely.
//	 *
//	 * @return void
//	 * @throws InvalidArgumentException
//	 */
//    public function render()
//    {
//		View::factory($this->template, array('form' => $this))->render();
//    }
//
//	public function hidden($name, $value = null)
//	{
//		$el = new Form_Field_Hidden($this->header, $name, $value);
//		return $this->header->elements[$name] =$el;
//	}
//
//	public function text($name, $label = null, $value = null)
//	{
//		$el = new Form_Field_Text($this->header, $name, $label, $value);
//		return $this->header->elements[$name] = $el;
//	}
//
//	public function password($name, $label = null, $value = null)
//	{
//		$el = new Form_Field_Password($this->header, $name, $label, $value);
//		return $this->header->elements[$name] = $el;
//	}
//
//	public function select($name, $options, $label = null, $value = null)
//	{
//		$el = new Form_Field_Select($this->header, $name, $options, $label, $value);
//		return $this->header->elements[$name] = $el;
//	}
//
//	public function radio($name, $options, $label = null, $value = null)
//	{
//		$el = new Form_Field_Radio($this->header, $name, $options, $label, $value);
//		return $this->header->elements[$name] = $el;
//	}
//
//	public function checkbox($name, $label = null, $boxLabel = null, $value = null)
//	{
//		$el = new Form_Field_Checkbox($this->header, $name, $label, $boxLabel, $value);
//		return $this->header->elements[$name] = $el;
//	}
//
//	public function textarea($name, $label = null, $value = null)
//	{
//		$el = new Form_Field_Textarea($this->header, $name, $label, $value);
//		return $this->header->elements[$name] = $el;
//	}
//
//	public function html($name, $label = null, $value = null)
//	{
//		$el = new Form_Field_Html($this->header, $name, $label, $value);
//		return $this->header->elements[$name] = $el;
//	}
//
//	public function submit($name, $value, $altLink = null)
//	{
//		$el = new Form_Field_Submit($this->header, $name, $value, $altLink);
//		return $this->header->elements[$name] = $el;
//	}
//}
