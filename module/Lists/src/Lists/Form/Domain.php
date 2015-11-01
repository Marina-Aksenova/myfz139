<?php
namespace Lists\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Domain extends Form
{
	const SUCCESS_MESSAGE = 'Данные успешно сохранены';

	public function __construct()
	{
		parent::__construct('create_domain');

		$this->setAttribute('method', 'post')
			 ->setHydrator(new ClassMethodsHydrator(false))
			 ->setInputFilter(new InputFilter());

		$this->add(array(
			'type' => 'Zend\Form\Element\Collection',
			'name' => 'domains',
			'options' => array(
				'count' => 1,
				'should_create_template' => true,
				'allow_add' => true,
				'target_element' => array(
					'type' => 'Lists\Fieldset\DomainFieldset',
				),
			),
		));

		$this->add(array(
			'type' => 'Zend\Form\Element\Csrf',
			'name' => 'csrf',
		));

		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'class' => 'btn btn-default',
				'value' => 'Сохранить',
			),
		));
	}
}