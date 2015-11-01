<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Auth\Controller;

use Auth\Adapter\DbTable;
use Auth\Form\Login;
use Auth\Model\AuthModel;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	public function indexAction()
	{
		return new ViewModel();
	}

	/**
	 * Makes client logged in
	 * @return array
	 * @throws \Exception
	 */
	public function loginAction()
	{
		$form = new Login();
		$err_message = $this->params()->fromRoute('redirectMessage');

		/** @var Request $request */
		$request = $this->getRequest();
		if ($request->isPost()) {

			$form->setData($request->getPost());
			$form->setInputFilter($form->inputFilter());

			if ($form->isValid()) {
				$serviceManager = $this->getServiceLocator();
				/** @var DbTable $authAdapter */
				$authAdapter = $serviceManager->get('authDbAdapter');

				$authAdapter->setIdentity($form->getData()['login']);
				$authAdapter->setCredential($form->getData()['pwd']);

				/** @var AuthenticationService $authService */
				$authService = $serviceManager->get('authService');
				/** @var AuthModel $authModel */
				$authModel = $serviceManager->get('authModel');

				// Check if login and password both right
				$err_message = Login::ERR_MESSAGE;
				if ($authService->authenticate($authAdapter)->isValid()) {
					$client = $authAdapter->getIdentities()[0];
					if ($client['blocked']) {
						$err_message = Login::ERR_MESSAGE_BLOCKED;
					} else {
						$authModel->save(['cnt_err_logins' => 0], $client['id']);

						$container = new Container('auth_user');
						$container->timeStart = time();

						$userContainer = new Container('user');
						if (!empty($userContainer->previousUrl)) {
							return $this->redirect()->toUrl($userContainer->previousUrl);
						}

						return $this->redirect()->toRoute('statistics/cl-statistic-often');
					}
					// If only login is right - increase wrong logins counter
				} else if ($authAdapter->getIdentities()) {
					$client = $authAdapter->getIdentities()[0];
					if ($client['blocked']) {
						$err_message = Login::ERR_MESSAGE_BLOCKED;
					} else {
						$wrongLogins = ++$client['cnt_err_logins'];
						$authModel->save(['cnt_err_logins' => $wrongLogins], $client['id']);
						if ($wrongLogins >= $this->getServiceLocator()->get('config')['loginAttempts']) {
							$err_message = Login::ERR_MESSAGE_WRONG_LOGINS;
							$authModel->save(['blocked' => 1], $client['id']);
						}
					}
				}
			}
		}

		return array('form' => $form, 'err_message' => $err_message);
	}

	/**
	 * Makes client logged out
	 * @return \Zend\Http\Response
	 */
	public function logoutAction()
	{
		$serviceManager = $this->getServiceLocator();
		/** @var AuthenticationService $authService */
		$authService = $serviceManager->get('authService');
		$authService->clearIdentity();
		$container = new Container('user');
		$container->previousUrl = 'statistics/cl-statistic-often';

		return $this->redirect()->toRoute('statistics/cl-statistic-often');
	}
}
