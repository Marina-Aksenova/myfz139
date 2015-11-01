<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Select;

abstract class AbstractTable extends AbstractTableGateway implements AdapterAwareInterface
{
	protected $table;
	protected $adapter;
	protected $connection;

	public function setDbAdapter(Adapter $adapter)
	{
		$this->adapter = $adapter;

		$this->resultSetPrototype = new HydratingResultSet();
		$this->initialize();

		$driver = $this->adapter->getDriver();
		$this->connection = $driver->getConnection();
	}

	/**
	 * Fetch all records from the table
	 */
	public function fetch($where = null)
	{
		$resultSet = $this->select($where);
		return $resultSet->current();
	}

	/**
	 * Fetch all records from the table
	 */
	public function fetchAll($where = null)
	{
		$resultSet = $this->select($where);
		return $resultSet->toArray();
	}

	/**
	 * Getting a table row by id
	 * @param $id - ID string
	 * @throw Exception
	 */
	public function get($id)
	{
		$id = (int)$id;
		$rowset = $this->select(array('id' => $id));
		$row = $rowset->current();
		if (!$row) throw new \Exception("Could not find row $id");

		return $row;
	}

	/**
	 * Getting a table row by id
	 * @param $id - ID string
	 * @throw Exception
	 */
	public function getById($id, $columnName = 'id')
	{
		$id = (int)$id;
		$rowset = $this->select(array($columnName => $id));
		$row = $rowset->current();
		if (!$row) throw new \Exception("Could not find row $id");

		return $row;
	}

	/**
	 * Saving data
	 * @param $data - an array whose keys are field names of the table, and values - their new values
	 * @param $id - ID-date records || false - addition (by default false)
	 * @throw Exception
	 */
	public function save($data, $id = false)
	{
		if ($id === false) {
			$this->insert($data);
		} else {
			if ($this->get($id)) $this->update($data, array('id' => $id));
			else throw new \Exception('Form id does not exist');
		}
	}

	/**
	 * Saving data
	 * @param $data - an array whose keys are field names of the table, and values - their new values
	 * @param $id - ID-date records || false - addition (by default false)
	 * @throw Exception
	 */
	public function saveById($data, $id = false, $columnName = 'id')
	{
		if ($id === false) {
			$this->insert($data);
		} else {
			if ($this->getById($id, $columnName)) $this->update($data, array($columnName => $id));
			else throw new \Exception('Form id does not exist');
		}
	}

	/**
	 * Removing records from the table
	 * @param $id - row identifier
	 */
	public function deleteById($id, $columnName = 'id')
	{
		$id = (int)$id;
		$this->delete(array($columnName => $id));
	}

	/**
	 * Gets guide
	 * @return array[array('id'=>'id', 'name'=>'name')]
	 */
	public function getGuide()
	{
		$resultSet = $this->select(function (Select $select) {
			$select->columns(array('id', 'name'));
		});

		return $resultSet->toArray();
	}

	public function beginTransaction()
	{
		return $this->connection->beginTransaction();
	}

	public function commit()
	{
		return $this->connection->commit();
	}

	public function rollBack()
	{
		return $this->connection->rollBack();
	}

	/**
	 * Call procedure
	 * @param $name - procedure's name
	 * @param $params - procedure's params
	 * @return array
	 */
	public function callProcedure($name, $params = false)
	{
		$query = "CALL $name";
		if (is_array($params) && count($params) > 0) {
			$param_str = '';
			foreach ($params as $param) {
				if ($param == null) $param = 'null';
				else $param = $this->adapter->platform->quoteValue($param);
				$param_str .= $param . ',';
			}
			$param_str = trim($param_str, ',');

			$query .= "($param_str)";
		} else {
			$query .= "()";
		}

		/** @var \Zend\Db\Adapter\Adapter $adapter */
		$adapter = $this->adapter;
		$r = $adapter->query($query, Adapter::QUERY_MODE_EXECUTE);

//		$result = $r->toArray();
		if ($r instanceof StatementInterface) {
			$result = $r->toArray();
		} else {
			if ((substr_compare($query, 'p_lk_del_clients_entity_rules', 5, 19)) === 0) {
				$result = [];
			} else $result = $r->toArray();
//			$result = $r->getAffectedRows();
//			if (!$result) {
//				throw new \Exception('Строки не были изменены процедурой');
//			}
		}

		return $result;
	}

}

?>