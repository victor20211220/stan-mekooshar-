<?php

/**
 * Controller.
 *
 * @version $Id$
 * @package Application
 */

class Captcha
{
        /**
         * @var string Available items.
         */
        private $items = array('penguin', 'bubblegum', 'parrot');

        /**
         * @var string Generated items.
         */
        private $objects = array();

        /**
         * @var string View.
         */
        private $view = 'common/capcha';
        private $src = 'resources/images/captcha/';

        private static $instance;

        private $pages = array();

        private $active = false;

        public function __construct($getFromSession = false)
        {
                if ('' === session_id()) {
                        session_start();
                }

                $this->generate($getFromSession);
        }

        public static function instance()
        {
                return empty(self::$instance) ? self::$instance = new self() : self::$instance;
        }

        public static function getCreatedInstance()
        {
                return self::$instance = new self(true);
        }

        /**
         * Generate new capcha.
         *
         * @return array
         */
        private function generate($getFromSession = false)
        {
                if(!$getFromSession && !Request::isPost()) {
                        $this->objects = array();
                        foreach($this->items as $v) {
                                $this->objects[$v] = Text::random('alpha', 5);
                        }
                        $_SESSION['captcha']['objects'] = $this->objects;
//                        $this->objects = $_SESSION['captcha']['objects'];
                } else {
                        $this->objects = $_SESSION['captcha']['objects'];
                }
        }

        public function regenerate()
        {
//		$this->generate(true);
                foreach($this->pages as $name => $value) {
                        $this->add($name, true);
                }
        }

        /**
         * Add new page
         *
         * @param string $name Name of page
         */
        public function add($name, $forceChange = false)
        {
                if(empty($this->pages[$name]) || $forceChange) {
                        if(!Request::isPost() || $forceChange) {
                                $this->pages[$name] = $_SESSION['captcha'][$name] = array_rand($this->objects);
                        } else {
                                $this->pages[$name] = $_SESSION['captcha'][$name];
                        }
                }
                $this->active = $name;

                return $this;
        }

        /**
         * Add new page
         *
         * @param string $name Name of page
         */
        public function get($name = false)
        {
                return $name ? $this->pages[$this->active] : array_flip($this->objects);
        }

        /**
         * Add new page
         *
         * @param string $name Name of page
         */
        public function getActive($getKey = false)
        {
                return $getKey ? $this->objects[$this->pages[$this->active]] : $this->pages[$this->active];
        }

        /**
         * Add new page
         *
         * @param string $name Name of page
         */
        public function field($form, $after = null)
        {

                $captcha = $this;
                $form->hidden('captcha')
                        ->attribute('for', 'captcha')
                        ->phantom()
                        ->before(function($field) use($captcha) {
                                        if(!$field->value || $field->value != $captcha->getActive(true)) {
                                                $field->fieldset->elements['captchaHtml']
                                                        ->rule(function($field) use($captcha) {
                                                                        return 'Select correct element';
                                                                });
                                        }
                                })
                        ->after(function($field) use($captcha) {
                                        $field->value = false;
//				$captcha->regenerate();
                                        $field->fieldset->elements['captchaHtml']->value = $captcha->render();
                                });

                $form->html('captchaHtml', false, $captcha->render());
        }

        /**
         * Render view.
         *
         * @return object View
         */
        public function render()
        {
                $objects = $this->objects;
                shuffle($objects);
                return new View($this->view, array('heroes' => $objects, 'select' => $this->getActive(), 'active' => $this->active));
        }

        /**
         * Render view.
         *
         * @return object View
         */
        public function src($name)
        {
                return $this->src . $name . '.png';
        }
}