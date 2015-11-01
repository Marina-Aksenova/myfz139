<?php
namespace Statistics\Model;

use Application\Model;

class StatisticModel extends Model\AbstractTable
{
	protected $table = 't_lk_control_lists_statistic';

	public function getSelectDomain($data)
	{
		return $this->callProcedure('p_lk_get_client_statistic', [$data['clientid'], $data['date_start'], $data['date_end'], 0, $data['limit']]);
	}

	public function getSelectGroup($data)
	{
		return $this->callProcedure('p_lk_get_client_group_statistic', [$data['group_id'], $data['date_start'], $data['date_end'], 0, $data['limit']]);
	}

	public function getSelectGroupOften($data)
	{
		return $this->callProcedure('p_lk_get_group_frequent_statistic', [$data['group_id'], 0, $data['limit']]);
	}

	public function getSelectClientOften($data)
	{
		return $this->callProcedure('p_lk_get_client_frequent_statistic', [$data['clientid'], 0, $data['limit']]);
	}

}