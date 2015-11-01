<?php
namespace Statistics\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;

class Statistic extends Form implements InputFilterAwareInterface
{
	const ERR_MESSAGE_PERIOD = 'Выберите период';
	const ERR_MESSAGE_DATE_FORMAT = 'Указанная дата или её формат неверны. Пример даты: 01.01.2000';

	public function __construct($name = null)
	{
		parent::__construct('client-statistic');

		$this->add(array(
			'name' => 'datePeriod',
			'type' => 'select',
			'options' => array(
				'value_options' => array(
					'0' => 'Сутки',
					'1' => 'Неделя',
					'2' => 'Месяц',
					'3' => '3 месяца',
					'4' => 'Год',
					'5' => 'Другой',
				),
			),
			'attributes' => array(
				'class' => 'form-control',
				'value' => '1' //set selected to week
			)
		));

		$this->add(array(
			'name' => 'dateCalendarFirst',
			'type' => 'text',
//			'type' => 'Zend\Form\Element\Date',
			'attributes' => array(
				'class' => 'form-control',
			),
			'options' => array(
				'label' => 'c',
			)
		));

		$this->add(array(
			'name' => 'dateCalendarLast',
			'type' => 'text',
//			'type' => 'Zend\Form\Element\Date',
			'attributes' => array(
				'class' => 'form-control',
			),
			'options' => array(
				'label' => 'по'
			),
		));

		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Применить',
				'class' => 'btn btn-default',
			),
		));
	}

	public function inputFilter()
	{
		$inputFilter = new InputFilter();

		$inputFilter->add(array(
			'name' => 'dateCalendarFirst',
			'required' => false,
//			'validators' => array(
//				array(
//					'name' => 'Between',
//					'options' => array(
//						'min' => '1970-01-01',
//						'max' => date('Y-m-d')
//					),
//				),
//			),
		));

		$inputFilter->add(array(
			'name' => 'dateCalendarLast',
			'required' => false,
//			'validators' => array(
//				array(
//					'name' => 'Between',
//					'options' => array(
//						'min' => '1970-01-01',
//						'max' => date('Y-m-d')
//					),
//				),
//			),
		));

		return $inputFilter;
	}
}