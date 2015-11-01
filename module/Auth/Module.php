<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Auth;

use Auth\Adapter\DbTable;
use Auth\Model\AuthModel;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Config\StandardConfig;
use Zend\Session\SessionManager;

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
				'authDbAdapter' => function (ServiceManager $serviceManager) {
					/** @var Adapter $dbAdapter */
					$dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
					$authAdapter = new DbTable($dbAdapter);
					$authAdapter->setTableName('t_lk_clients');
					$authAdapter->setIdentityColumn('login');
					$authAdapter->setCredentialColumn('pwd');

					return $authAdapter;
				},
				'authService' => function (ServiceManager $serviceManager) {
					$sessionConfig = new SessionConfig();
					$manager = new SessionManager($sessionConfig);
//					$manager->rememberMe(5);
					$storage = new Session('auth_user', 'auth_user', $manager);
					$auth = new AuthenticationService();
					$auth->setStorage($storage);

					return $auth;
				},
				'authModel' => function (ServiceManager $serviceManager) {
					return new AuthModel();
				},
			),
		);
	}
}
