<?php
namespace Lists\Fieldset;

use Lists\Entity\Domain as DomainEntity;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\Regex;

class DomainFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function __construct($name = null)
	{
		parent::__construct('domain');

		$this->setAttribute('class', 'form-horizontal')
			 ->setHydrator(new ClassMethodsHydrator(false))
			 ->setObject(new DomainEntity());

		$this->add(array(
			'name' => 'resource',
			'type' => 'text',
			'options' => array(
				'label' => 'Домены',
			),
			'attributes' => array(
				'class' => 'form-control input-resource',
			),
		));
		$this->add(array(
			'name' => 'description',
			'type' => 'text',
			'options' => array(
				'label' => 'Описание',
			),
			'attributes' => array(
				'class' => 'form-control input-description',
			),
		));
		$this->add(array(
			'name' => 'remove',
			'attributes' => array(
				'type' => 'button',
				'class' => 'glyphicon glyphicon-remove domain-remove col-sm-1 form-control',
			),
		));
	}

	/**
	 * Should return an array specification compatible with
	 * {@link Zend\InputFilter\Factory::createInputFilter()}.
	 *
	 * @return array
	 */
	public function getInputFilterSpecification()
	{
		return array(
			'resource' => array(
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
			),
			'description' => array(
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
			),
		);

	}

}