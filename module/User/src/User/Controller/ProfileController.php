<?php
namespace User\Controller;

use User\Form\Password;
use User\Model\ProfileModel;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use User\Form\Profile;
use Zend\Db\Sql;
use Zend\Stdlib\ArrayUtils;

class ProfileController extends AbstractActionController
{
	/**
	 * Build profile form
	 * @return array
	 * @throws \Exception
	 */
	public function indexAction()
	{
		$serviceManager = $this->getServiceLocator();
		$form = new Profile();
		$message = '';
		/** @var AuthenticationService $authService */
		$authService = $serviceManager->get('authService');
		$client = $authService->getIdentity()['id'];
		/** @var ProfileModel $profileModel */
		$profileModel = $serviceManager->get('profileModel');
		$clientLogin = $profileModel->getById($client, $columnName = 'id')['login'];

		/** @var Request $request */
		$request = $this->getRequest();
		if ($request->isPost()) {

			$form->setData($request->getPost());
			$form->setInputFilter($form->inputFilter());

			if ($form->isValid()) {
				/** @var ProfileModel $profileModel */
				$profileModel = $serviceManager->get('profileModel');
				$formData = $form->getData();
				$dataToSave = [
					'name' => $formData['name'],
					'client_email' => $formData['client_email'],
					'type_list' => $formData['type_list'],
				];
				$profileModel->save($dataToSave, $authService->getIdentity()['id']);
				$message = Profile::SUCCESS_MESSAGE;
				unset($formData['submit']);
				$newUserData = ArrayUtils::merge($authService->getIdentity(), $formData);

				$authService->getStorage()->write($newUserData);
			}
		} else {
			$form->setData($authService->getIdentity());
			if ($this->flashMessenger()->hasSuccessMessages()) {
				$message = $this->flashMessenger()->getSuccessMessages()[0];
			}
		}

		return array('form' => $form, 'clientLogin' => $clientLogin, 'message' => $message,);
	}

	/**
	 * Build form for changing password
	 * @return array|\Zend\Http\Response
	 * @throws \Exception
	 */
	public function passwordAction()
	{
		$serviceManager = $this->getServiceLocator();
		/** @var Password $form */
		$form = $serviceManager->get('passwordForm');

		/** @var Request $request */
		$request = $this->getRequest();
		if ($request->isPost()) {

			$form->setData($request->getPost());
			$form->setInputFilter($form->inputFilter());

			if ($form->isValid()) {
				/** @var AuthenticationService $authService */
				$authService = $serviceManager->get('authService');
				/** @var ProfileModel $profileModel */
				$profileModel = $serviceManager->get('profileModel');

				$profileModel->save(['pwd' => md5($form->getData()['password'])], $authService->getIdentity()['id']);
				$this->flashMessenger()->addSuccessMessage(Profile::SUCCESS_MESSAGE);

				return $this->redirect()->toRoute('user/profile', ['controller' => 'profile']);
			}
		}
		return array('form' => $form);
	}
}