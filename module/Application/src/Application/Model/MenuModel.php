<?php
namespace Application\Model;

use Application\Model\AbstractTable;
use Zend\Db\Sql\Select;

class MenuModel extends AbstractTable
{
	protected $table = 't_lk_pages';

	/**
	 * Fetch menu items
	 * @param $where
	 * @return array|\Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll($where = null)
	{
		$resultSet = $this->select(function (Select $select){
			$select->where(['isActive' => 1]);
			$select->order(['ord']);
		});

		$resultSet = $resultSet->toArray();

		return $resultSet;
	}
}