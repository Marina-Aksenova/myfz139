<?php
namespace Auth\Adapter;

use Zend\Authentication\Adapter\DbTable\AbstractAdapter;
use Zend\Authentication\Result;
use Zend\Db\Sql\Select;
use Zend\Db\Sql;

class DbTable extends AbstractAdapter
{
	private $createSelect;
	private $resultIdentities;

	/**
	 * @inheritdoc
	 */
	protected function authenticateValidateResult($resultIdentity)
	{
		$code = Result::SUCCESS;
		$passwordHash = $resultIdentity['pwd'];

//		$bcrypt = new Bcrypt();
		if ($this->verify($this->credential, $passwordHash)) {
			$code = Result::FAILURE_CREDENTIAL_INVALID;
		}
		unset($resultIdentity['pwd']);

		return new Result($code, $resultIdentity);
	}

	/**
	 * Compare params
	 * @param $password
	 * @param $hash
	 * @return int 0 - true, 1 - false
	 */
	protected function verify($password, $hash)
	{
		$result = md5($password);
		return strcmp($result, $hash);
	}

	/**
	 * @inheritdoc
	 */
	protected function authenticateCreateSelect()
	{
		if (!$this->createSelect) {
			$sql = new Select($this->tableName);
			$sql->where(['login' => $this->getIdentity()]);
			$this->createSelect = $sql;
		}

		return $this->createSelect;
	}

	/**
	 * @inheritdoc
	 */
	protected function authenticateQuerySelect(Sql\Select $dbSelect)
	{
		if (!$this->resultIdentities) {
			$this->resultIdentities = parent::authenticateQuerySelect($dbSelect);
		}

		return $this->resultIdentities;
	}

	/**
	 * Get identities
	 * @return array
	 */
	public function getIdentities()
	{
		$dbSelect = $this->authenticateCreateSelect();
		$resultIdentities = $this->authenticateQuerySelect($dbSelect);

		return $resultIdentities;
	}
}