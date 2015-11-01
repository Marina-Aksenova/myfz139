<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Auth\Form\Login;
use Application\Model\LayoutModel;
use User\Model\ProfileModel;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\Validator\AbstractValidator;
use Zend\View\Helper\Navigation;
use Zend\View\Helper\PaginationControl;
use Application\Model\MenuModel;
use Application\Navigation\MyNavigation;
use Zend\Mvc\Controller\AbstractActionController;

class Module
{
	public function onBootstrap(MvcEvent $e)
	{
		$eventManager = $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);

		$eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'));
		$eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'authorization'), 2);
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

	/**
	 * Authorization
	 * @param MvcEvent $e
	 */
	public function authorization(MvcEvent $e)
	{
		$serviceManager = $e->getApplication()->getServiceManager();
		/** @var \Zend\Authentication\AuthenticationService $authService */
		$authService = $serviceManager->get('authService');
		$redirectMessage = null;
		$timeout = null;
		/** @var Request $request */
		$request = $e->getRequest();
		$path = $request->getUri()->getPath();

		$container = new Container('user');
		if ($path !== $e->getRouter()->assemble([], ['name' => 'login'])) {
			$container->previousUrl = $path;
		}

		if ($authService->hasIdentity()) {
			$container = new Container('auth_user');

			if (($container->timeStart + $serviceManager->get('config')['sessionLifetime']) < time()) {
				$authService->clearIdentity();
				$redirectMessage = Login::ERR_MESSAGE_LIFETIME;
				$timeout = 1;
			} else {
				$container->timeStart = time();
			}

//			$client = $authService->getIdentity();
//			$show_group_stat = (int)$client['show_group_stat'];
			$clientId = $authService->getIdentity()['id'];
			/** @var ProfileModel $profileModel */
			$profileModel = $serviceManager->get('profileModel');
			$show_group_stat = (int)$profileModel->getById($clientId, $columnName = 'id')['show_group_stat'];
			if (in_array($path, ['/statistics/gr-statistic', '/statistics/gr-statistic-often']) && $show_group_stat == 0) {
				$redirectMessage = Login::ERR_MESSAGE_FORBIDDEN;
				$authService->clearIdentity();
				$container = new Container('user');
				$container->previousUrl = '/statistics/cl-statistic-often';
			}
		}
		if (!$authService->hasIdentity()) {
			if ($path != '/login' && !$timeout) {
				$redirectMessage = Login::ERR_MESSAGE_FORBIDDEN;
			}
		}

		if ($redirectMessage) {
			$route = Literal::factory(array(
				'route' => $path,
				'defaults' => array(
					'controller' => 'Auth\Controller\Index',
					'action' => 'login',
					'redirectMessage' => $redirectMessage
				),
			));
			$e->getRouter()->addRoute('access_error_auth', $route);
		}
	}

	public function onDispatch(MvcEvent $e)
	{
		$routeParams = $e->getRouteMatch()->getParams();
		$serviceManager = $e->getApplication()->getServiceManager();
		/** @var \Zend\Authentication\AuthenticationService $authService */
		$authService = $serviceManager->get('authService');
		$isLoginRequest = $routeParams['controller'] === 'Auth\Controller\Index' && $routeParams['action'] === 'login';

		if ($authService->hasIdentity() && $isLoginRequest) {
			return $this->redirect($e, [], ['name' => 'statistics/cl-statistic-often']);
		}

		PaginationControl::setDefaultViewPartial('layout/myPagination');
	}

	private function redirect(MvcEvent $e, $routeParams, $routeOptions)
	{
		/** @var Response $response */
		$response = $e->getResponse();
		$response->getHeaders()->addHeaderLine('Location', $e->getRouter()->assemble($routeParams, $routeOptions));
		$response->setStatusCode(302);
		$response->sendHeaders();

		return false;
	}

	public function getServiceConfig()
	{
		return array(
			'factories' => array(
				'layoutModel' => function (ServiceManager $serviceManager) {
					return new LayoutModel();
				},
				'menuModel' => function (ServiceManager $serviceManager) {
					return new MenuModel();
				},
				'Navigation' => function (ServiceManager $serviceManager) {
					$navigation = new MyNavigation();
					return $navigation->createService($serviceManager);
				}
			),
		);
	}
}