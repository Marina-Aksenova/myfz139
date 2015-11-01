<?php
namespace Lists\Controller;

use Zend\Db\Sql;
use Zend\Stdlib\ArrayUtils;

class BlackListController extends ListController
{
	protected $type = '1';

	protected $controller = 'black-list';

}