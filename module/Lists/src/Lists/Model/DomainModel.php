<?php
namespace Lists\Model;

use Application\Model;

class DomainModel extends Model\AbstractTable
{
	protected $table = 't_lk_entity_rules';

	protected $type;

	protected $controller;

	public function setController($controller)
	{
		$this->controller = $controller;
		return $this;
	}

	public function getController()
	{
		return $this->controller;
	}

	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	public function getType()
	{
		return $this->type;
	}

	public function addDomain($data)
	{
		return $this->callProcedure('p_lk_add_clients_entity_rules', [$data['resource'],$data['description'],$data['clientid'],$data['type_block']]);
	}

	public function delDomain($data)
	{
		return $this->callProcedure('p_lk_del_clients_entity_rules', [$data['er_id']]);
	}

	public function getDomain($clientId)
	{
		return $this->fetchAll(['clientid' => $clientId, 'type_block' => $this->type , 'deleted' => 0]);
	}
}