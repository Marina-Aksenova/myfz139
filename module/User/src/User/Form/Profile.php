<?php
namespace User\Form;

use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\Validator\Hostname;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;

class Profile extends Form implements InputFilterAwareInterface
{
	const SUCCESS_MESSAGE = 'Данные успешно сохранены';

	public function __construct($name = null)
	{
		parent::__construct('profile');

		$this->add(array(
			'name' => 'login',
			'type' => 'Text',
			'options' => array(
				'label' => 'Логин',
			),
		));
		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'options' => array(
				'label' => 'Компания',
				'label_attributes' => array('class' => 'control-label')
			),
			'attributes' => [
				'class' => 'col-sm-2 form-control',
			],
		));
		$this->add(array(
			'name' => 'client_email',
			'type' => 'Text',
			'options' => array(
				'label' => 'Email',
			),
			'attributes' => [
				'class' => 'col-sm-2 form-control',
			],
		));
		$this->add(array(
			'name' => 'type_list',
			'type' => 'Zend\Form\Element\Radio',
			'options' => array(
				'value_options' => array(
					'0' => 'Блокировка по разрешенным спискам',
					'1' => 'Блокировка по черным спискам',
				),
			),
		));
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Сохранить',
				'id' => 'submitbutton',
			),
		));
	}

	public function inputFilter()
	{
		$inputFilter = new InputFilter();

		$inputFilter->add(array(
			'name' => 'name',
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
			'name' => 'client_email',
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
					'name' => 'EmailAddress',
					'options' => array(
						'messages' => [
							EmailAddress::INVALID            => "Неверное значение",
							EmailAddress::INVALID_FORMAT     => "Неверный формат email",
							EmailAddress::INVALID_HOSTNAME   => "Неверный формат email",
							EmailAddress::INVALID_MX_RECORD  => "Неверный формат email",
							EmailAddress::INVALID_SEGMENT    => "Неверный формат email",
							EmailAddress::DOT_ATOM           => "Неверный формат email",
							EmailAddress::QUOTED_STRING      => "Неверный формат email",
							EmailAddress::INVALID_LOCAL_PART => "Неверный формат email",
							EmailAddress::LENGTH_EXCEEDED    => "Превышена допустимая длина",
							Hostname::CANNOT_DECODE_PUNYCODE  => "Неверный формат email",
							Hostname::INVALID                 => "Неверный формат email",
							Hostname::INVALID_DASH            => "Неверный формат email",
							Hostname::INVALID_HOSTNAME        => "Неверный формат email",
							Hostname::INVALID_HOSTNAME_SCHEMA => "Неверный формат email",
							Hostname::INVALID_LOCAL_NAME      => "Неверный формат email",
							Hostname::INVALID_URI             => "Неверный формат email",
							Hostname::IP_ADDRESS_NOT_ALLOWED  => "Неверный формат email",
							Hostname::LOCAL_NAME_NOT_ALLOWED  => "Неверный формат email",
							Hostname::UNDECIPHERABLE_TLD      => "Неверный формат email",
							Hostname::UNKNOWN_TLD             => "Неверный формат email",
						],
					),
				),
			),
		));

		return $inputFilter;
	}
}