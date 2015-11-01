<?php
namespace Lists\Controller;

use Zend\Db\Sql;
use Zend\Stdlib\ArrayUtils;

class WhiteListController extends ListController
{
	protected $type = '0';

	protected $controller = 'white-list';

}