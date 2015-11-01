<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class Password extends Form implements InputFilterAwareInterface
{
	public function __construct($name = null)
	{
		parent::__construct('password');

		$this->add(array(
			'name' => 'password_old',
			'type' => 'password',
			'options' => array(
				'label' => 'Старый пароль',
			),
		));
		$this->add(array(
			'name' => 'password',
			'type' => 'password',
			'options' => array(
				'label' => 'Новый пароль',
			),
		));
		$this->add(array(
			'name' => 'password_confirm',
			'type' => 'password',
			'options' => array(
				'label' => 'Подтверждение пароля',
			),
		));
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Применить',
				'id' => 'submitbutton',
			),
		));
	}

	public function inputFilter()
	{
		$inputFilter = new InputFilter();

		$inputFilter->add(array(
			'name' => 'password_old',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'min' => 1,
						'max' => 128,
					),
				),
				array(
					'name' => 'Callback',
					'options' => array(
						'callback' => function ($oldPassword) {
							return md5($oldPassword) === $this->getOption('oldPassword');
						},
					),
				),
			),
		));

		$inputFilter->add(array(
			'name' => 'password',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'min' => 1,
						'max' => 128,
					),
				),
			),
		));

		$inputFilter->add(array(
			'name' => 'password_confirm',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'min' => 1,
						'max' => 128,
					),
				),
				array(
					'name' => 'Identical',
					'options' => array(
						'token' => 'password',
					),
				),
			),
		));

		return $inputFilter;
	}
}