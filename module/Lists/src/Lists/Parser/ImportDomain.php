<?php
namespace Lists\Parser;

use Lists\Model\DomainModel;
use Zend\Di\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class ImportDomain
{
	private $errors = [];

	/**
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * @param array $error
	 */
	private function addError($error)
	{
		$this->errors[] = $error;
	}

	/**
	 * Parse *.txt file
	 * @param $file
	 * @return array|bool
	 */
	public function parse($file)
	{
		$domainsResources = [];

		try {
			if (!$lines = file($file)) {
				throw new \Exception('Пустой файл');
			}

			foreach ($lines as $line) {
				$trimedLine = trim($line);
				$trimedLine = iconv('windows-1251', 'utf-8', $trimedLine);
				if ($trimedLine === "") {
					continue;
				}
				if (!preg_match('/[^\s.](\.[^\s.])/', $trimedLine)) {
					throw new \Exception('Неверное значение домена "' . $trimedLine . '"');
				}
				$domainsResources[] = $trimedLine;
			}
			return $domainsResources;
		} catch (\Exception $exception) {
			$this->addError($exception->getMessage());
			return false;
		}
	}
}