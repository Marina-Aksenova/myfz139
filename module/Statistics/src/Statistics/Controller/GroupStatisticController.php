<?php
namespace Statistics\Controller;

use Zend\Db\Sql;
use Zend\Stdlib\ArrayUtils;

class GroupStatisticController extends StatisticController
{
	protected $group = true;
}