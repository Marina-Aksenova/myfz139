<?php
namespace User\Controller;

use User\Model\ProfileModel;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use User\Form\Profile;
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