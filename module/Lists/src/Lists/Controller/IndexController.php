<?php
namespace Lists\Controller;

use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Adapter\DbTable;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Sql;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	public function indexAction()
	{
		return new ViewModel();
	}
}