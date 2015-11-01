<?php
namespace Auth\Form;

use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Captcha;

class Login extends Form implements InputFilterAwareInterface
{
	const ERR_MESSAGE = 'Неверный логин или пароль';
	const ERR_MESSAGE_WRONG_LOGINS = 'Превышено количество попыток неверного входа, клиент заблокирован';
	const ERR_MESSAGE_BLOCKED = 'Клиент заблокирован';
	const ERR_MESSAGE_LIFETIME = 'Превышено время бездействия';
	const ERR_MESSAGE_FORBIDDEN = 'Недостаточно прав';

	public function __construct($name = null)
	{
		parent::__construct('login');

		$this->add(array(
			'name' => 'login',
			'type' => 'Text',
			'options' => array(
				'label' => 'Логин',
			),
		));
		$this->add(array(
			'name' => 'pwd',
			'type' => 'Text',
			'options' => array(
				'label' => 'Пароль',
			),
		));
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Go',
				'id' => 'submitbutton',
			),
		));
		// Captcha
		$this->add(array(
			'type' => 'Zend\Form\Element\Captcha',
			'name' => 'captcha',
			'options' => array(
				'label' => 'Введите текст с картинки',
				'captcha' => array(
					'class' => 'Figlet',
					'wordLen' => 3,
					'timeout' => 30,
					'messages' => [
						Captcha\AbstractWord::BAD_CAPTCHA => "Неверное значение",
						Captcha\AbstractWord::MISSING_VALUE => "Неверное значение",
						Captcha\AbstractWord::MISSING_ID => "Неверный идентификатор",
					],
				),
			),
		));
	}

	public function inputFilter()
	{
		$inputFilter = new InputFilter();

		$inputFilter->add(array(
			'name' => 'login',
			'required' => true,
			'filters' => array(
				array('name' => 'StringTrim'),
			),
			'validators' => array(
				array(
					'name' => 'NotEmpty',
					'options' => array(
						'messages' => [
							NotEmpty::IS_EMPTY => "Поле является обязательным и не может быть пустым",
							NotEmpty::INVALID => "Недопустимый тип данных",
						],
					),
				),
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'min' => 1,
						'max' => 64,
						'messages' => [
							StringLength::INVALID => "Недопустимый тип данных",
							StringLength::TOO_SHORT => "Длина меньше, чем %min% символов",
							StringLength::TOO_LONG => "Длина больше, чем %max% символов",
						],
					),
				),
			),
		));

		$inputFilter->add(array(
			'name' => 'pwd',
			'required' => true,
			'filters' => array(
				array('name' => 'StringTrim'),
			),
			'validators' => array(
				array(
					'name' => 'NotEmpty',
					'options' => array(
						'messages' => [
							NotEmpty::IS_EMPTY => "Поле является обязательным и не может быть пустым",
							NotEmpty::INVALID => "Недопустимый тип данных",
						],
					),
				),
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'min' => 1,
						'max' => 128,
						'messages' => [
							StringLength::INVALID => "Недопустимый тип данных",
							StringLength::TOO_SHORT => "Длина меньше, чем %min% символов",
							StringLength::TOO_LONG => "Длина больше, чем %max% символов",
						],
					),
				),
			),
		));

		$inputFilter->add(array(
			'name' => 'captcha',
			'required' => true,
		));

		return $inputFilter;
	}
}