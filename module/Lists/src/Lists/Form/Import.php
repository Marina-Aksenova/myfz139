<?php
namespace Lists\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\Validator\File\Extension;
use Zend\Validator\File\UploadFile;

class Import extends Form implements InputFilterAwareInterface
{
	public function __construct($name = null)
	{
		parent::__construct('import');

		// File Input
		$this->add(array(
			'type' => 'Zend\Form\Element\File',
			'name' => 'file',
			'options' => array(
				'label' => 'Файл импорта*',
				'file' => array(
					'class' => 'File',
				),
			),
		));

		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Выполнить импорт',
				'class' => 'btn btn-default',
			),
		));
	}

	public function inputFilter()
	{
		$inputFilter = new InputFilter();

		$inputFilter->add(array(
			'name' => 'file',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'Zend\Validator\File\UploadFile',
					'options' => array(
						'messages' => [
							UploadFile::NO_FILE => "Файл не загружен",
						],
					),
				),
				array(
					'name' => 'Zend\Validator\File\Extension',
					'options' => array(
						'extension' => ['txt'],
						'messages' => [
							Extension::FALSE_EXTENSION => "Файл имеет недопустимое расширение",
							Extension::NOT_FOUND => "Файл не читается или не загружен",
						],
					),
				),
			),
		));

		return $inputFilter;
	}
}