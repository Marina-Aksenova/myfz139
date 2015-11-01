<?php
namespace Lists\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\Validator\Digits;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\Regex;

class DomainUpdate extends Form implements InputFilterAwareInterface
{
	const SUCCESS_MESSAGE = 'Данные успешно сохранены';
	const INFO_MESSAGE = 'Домен удален';
	const ERROR_MESSAGE = 'Выберите домены для удаления';

	public function __construct($name = null)
    {
        parent::__construct('domain');

        $this->setAttribute('class', 'form-horizontal');

        $this->add(array(
            'name' => 'resource',
            'type' => 'Text',
            'options' => array(
                'label' => 'Домены',
            ),
            'attributes' => array(
                'class' => 'form-control input-resource',
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Text',
            'options' => array(
                'label' => 'Описание',
            ),
            'attributes' => array(
                'class' => 'form-control input-description',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Сохранить',
                'class' => 'btn btn-default',
            ),
        ));
    }

	public function inputFilter()
	{
		$inputFilter = new InputFilter();

		$inputFilter->add(array(
			'name' => 'resource',
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
				array(
					'name'    => 'Regex',
					'options' => array(
						'pattern' => '/[^\s.](\.[^\s.])/',
						'messages' => [
							Regex::INVALID   => "Неверное значение",
							Regex::NOT_MATCH => "Неверное значение",
							Regex::ERROROUS  => "Неверное значение",],
					),
				),
			),
		));

		$inputFilter->add(array(
			'name' => 'description',
			'required' => false,
			'filters' => array(
				array('name' => 'StringTrim'),
			),
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 128,
						'messages' => [
							StringLength::INVALID => "Недопустимый тип данных",
							StringLength::TOO_LONG => "Длина больше, чем %max% символов",
						],
					),
				),
			),
		));

		return $inputFilter;
	}
}