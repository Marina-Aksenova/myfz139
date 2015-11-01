<?php
namespace Application\View\Helper;

use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Application\Model\LayoutModel;
use Zend\Db\Sql;

class Layout extends AbstractHelper implements ServiceLocatorAwareInterface
{
	private $sm;
	private $data;

	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->sm = $serviceLocator->getServiceLocator();
	}

	public function getServiceLocator()
	{
		return $this->sm;
	}

	public function __invoke()
	{
		return $this;
	}

	public function getUserName() {
		/** @var AuthenticationService $authService */
		$authService = $this->sm->get('authService');
		$userName = $authService->getIdentity()['name'];
		return $userName;
	}

//	public function getUserEmail() {
//		/** @var AuthenticationService $authService */
//		$authService = $this->sm->get('authService');
//		$userEmail = $authService->getIdentity()['client_email'];
//		return $userEmail;
//	}

	public function getIdentity()
	{
		$serviceManager = $this->getView()->getHelperPluginManager()->getServiceLocator();
		/** @var AuthenticationService $authService */
		$authService = $serviceManager->get('authService');
		return $authService->getIdentity();
	}

	public function getData()
	{
		if (!$this->data) {
			$serviceManager = $this->getView()->getHelperPluginManager()->getServiceLocator();
			/** @var LayoutModel $layoutModel */
			$layoutModel = $serviceManager->get('layoutModel');

			$this->data = $layoutModel->fetch();
		}

		return $this->data;
	}

	public function getLogoSource()
	{
		$data = $this->getData();
		$logoSource = $data['logo'];
		if ($logoSource) {
			$logoSource = 'data:image/png;base64,' . base64_encode($logoSource);
		} else {
			$logoSource  = '/img/logo.png';
		}

		return $logoSource;
	}
}