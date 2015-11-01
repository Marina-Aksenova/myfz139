<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

use User\Form\Password;
use User\Model\ProfileModel;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Authentication\AuthenticationService;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

	public function getServiceConfig()
	{
		return array(
			'factories' => array(
                'profileModel' => function (ServiceManager $serviceManager) {
                    return new ProfileModel();
                },
                'passwordForm' => function (ServiceManager $serviceManager) {
                    $authService = $serviceManager->get('authService');
                    $passwordForm = new Password();
                    return $passwordForm->setOption('oldPassword', $this->bdPwd($serviceManager));
                },
			),
		);
	}

    /**
     * Get client password
     * @param ServiceManager $serviceManager
     * @return mixed
     * @throws \Exception
     */
	public function bdPwd(ServiceManager $serviceManager)
	{
		/** @var AuthenticationService $authService */
		$authService = $serviceManager->get('authService');
		$client = $authService->getIdentity()['id'];

		/** @var ProfileModel $profileModel */
		$profileModel = $serviceManager->get('profileModel');
		$clientPwd = $profileModel->getById($client, $columnName = 'id')['pwd'];

		return $clientPwd;
	}
}
