<?php

class Form_Messages_NewMessage extends Form_Main
{

	public $message = false;

	public function __construct($connections)
	{
		$form = new Form('newmessage', false, Request::generateUri('messages', 'new') . Request::getQuery());
		$this->form =& $form;

		$form->fieldset('fields', false, array('class' => 'customform on-white customform-label'));

		$toFriend = '';
        $aboutFriend = '';
        $readonly = '';
		if(! empty($connections['0']['userId'])){
            $toFriend = $connections['0']['userId'];
            $aboutFriend = Model_User::getById($connections['0']['userId']);
            $aboutFriend = $aboutFriend->firstName . ' ' . $aboutFriend->lastName;
            $readonly = 'readonly';
        }

		$form->hidden('selectedConnection', $toFriend)
			->attribute('class', 'localsearch-user_name-id');

//		$text = '';
//		foreach($connections['data'] as $connection) {
//			$text .= '<li>' . View::factory('parts/userava-more', array(
//					'isCustomInfo' => TRUE,
//					'isTooltip' => FALSE,
//					'avasize' => 'avasize_52',
//					'ouser' => $connection
//				)) . '</li>';
//		}
//
//		$text = '<ul class="localsearch localsearch-user_name">' . $text .  '</ul>';
//
//		$form->html('message_connections', false, $text)
//			->visible(false)
//			->attribute('class', 'form-html');
		$this->generateList($connections);

//		$form->html('message_to', false, '<b>Message to:</b>')
//			->attribute('class', 'form-html');

		$form->text('userName', 'Message to', $aboutFriend)
//			->attribute('placeholder', 'User Name')
			->attribute('tabindex', '1')
			->attribute('class', 'form-local-search')
			->attribute('data-localsearch-hidden', '.localsearch-user_name-id')
			->attribute('data-localsearch-list', '.localsearch-user_name')
			->attribute('data-localsearch-item', '.userava-name')
			->attribute('required', 'required')
            ->attribute($readonly)
			->required();

//		$form->html('message_subject', false, '<b>Subject:</b>')
//			->attribute('class', 'form-html');

		$form->text('subject', 'Subject')
			->attribute('tabindex', '2')
			->attribute('placeholder', '')
			->attribute('required', 'required')
			->required();


//		$form->html('message_text', false, '<b>Message text:</b>')
//			->attribute('class', 'form-html');

		$form->textarea('message', 'Message')
			->attribute('placeholder', '')
			->attribute('rows', '5')
			->attribute('tabindex', '3')
			->attribute('maxlength', '1000')
			->attribute('class', 'max-1000')
			->attribute('required', 'required')
			->rule('maxLength', 1000)
			->required();


		$form->fieldset('submit', false, array('class' => 'submit'));
		$form->html('history', ' ');
		$form->html('save', '<a class="btn-roundblue" href="#" onclick="$(this).closest(\'form\').find(\'input:submit\').click(); return false;">Send message</a>');
		$form->submit('submit', 'Submit')
			->visible(false);
		$this->form = $form;

		return $this;
	}

	public function setReplay($message_id){
		$user = Auth::getInstance()->getIdentity();
		$message = Model_Messages::getItemReplayByMessageId($message_id, $user->id);
		$this->message = $message;

		$this->form->elements['selectedConnection']->setValue($message->userId);
		$this->form->elements['userName']->setValue($message->userFirstName . ' ' . $message->userLastName);

		if(substr(trim($message->subject), 0, 3) != 'RE:') {
			$this->form->elements['subject']->setValue('RE: ' . trim($message->subject));
		} else {
			$this->form->elements['subject']->setValue(trim($message->subject));
		}

		$this->form->fieldset('submit', false, array('class' => 'submit'));
		$this->form->html('history', '<a href="' . (Request::generateUri('messages', 'history', $message->userId)) . '"  class="btn-roundblue-border icons i-historycustom' . ((Request::get('page', false)) ? 'hidden' : '') . '" onclick="$(this).addClass(\'hidden\'); $(\'.messages-history\').removeClass(\'hidden\'); return false;"><span></span>History</a>');
	}

	public function hideLabel()
	{
		unset($this->form->fieldsets['fields']->elements['message_text']);
		unset($this->form->fieldsets['fields']->elements['message_subject']);
		unset($this->form->fieldsets['fields']->elements['message_to']);
		unset($this->form->fieldsets['submit']->elements['history']);
	}

	public function setSentTo($connection)
	{
		$this->form->elements['selectedConnection']->setValue($connection->id);
		$this->form->elements['userName']->setValue($connection->userFirstName . ' ' . $connection->userLastName);
	}

	public function fromBox()
	{
		$this->form->attribute('onsubmit', "return box.submit(this);");
	}

	public function generateList($connections)
	{
		$form =& $this->form;
		$text = '';
		foreach($connections['data'] as $connection) {
			$text .= '<li>' . View::factory('parts/userava-more', array(
					'isCustomInfo' => TRUE,
					'isTooltip' => FALSE,
					'avasize' => 'avasize_52',
					'ouser' => $connection
				)) . '</li>';
		}

		$text = '<ul class="localsearch localsearch-user_name">' . $text .  '</ul>';

		$form->html('message_connections', false, $text)
			->visible(false)
			->attribute('class', 'form-html');
	}
}