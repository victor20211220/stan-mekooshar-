<?
if (isset($messages) and !empty($messages)) {
	foreach ($messages as $message) {
		list($text, $type) = $message;
		switch ($type) {
			case Controller::MESSAGE_WARNING:
				$class = 'box-warning';
				break;
			case Controller::MESSAGE_ERROR:
				$class = 'box-error';
				break;
			default:
				$class = 'box-info';
		}
		echo '<div class="box-mesasges ' . $class . '"><a href="#" class="box-closer">&times;</a>' . Html::chars($text) . '</div>';
	}
}
?>