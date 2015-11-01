<?php
namespace Lists\Controller;

use Lists\Model\DomainModel;
use Lists\Parser\ImportDomain;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Sql;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Lists\Entity\Domain as DomainEntity;
use Lists\Entity\Domains;
use Lists\Form\Domain;
use Lists\Form\DomainUpdate;
use Lists\Form\Import;
use Lists\Form\DomainFieldset;
use Zend\Di\Exception\ClassNotFoundException;

abstract class ListController extends AbstractActionController
{
	protected $type;

	protected $controller;

	/**
	 * Makes view for index page
	 * @return ViewModel
	 */
	public function indexAction()
	{
		$serviceManager = $this->getServiceLocator();
		/** @var DomainModel $domainModel */
		$domainModel = $serviceManager->get('domainModel');

		/** @var AuthenticationService $authService */
		$authService = $serviceManager->get('authService');
		$clientId = $authService->getIdentity()['id'];

		$message = '';
		if ($this->flashMessenger()->hasSuccessMessages()) {
			$message = $this->flashMessenger()->getSuccessMessages()[0];
		} else if ($this->flashMessenger()->hasInfoMessages()) {
			$message = $this->flashMessenger()->getInfoMessages()[0];
		}
		$domainModel->setType($this->type);
		$paginator = new Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($domainModel->getDomain($clientId)));

		$paginator->setItemCountPerPage(5);
		$paginator->setCurrentPageNumber($this->params('page', 1));

		return new ViewModel(['message' => $message, 'paginator' => $paginator]);
	}

	/**
	 * Remove domains
	 * @return \Zend\Http\Response
	 */
	public function removeAction()
	{
		$serviceManager = $this->getServiceLocator();
		/** @var DomainModel $domainModel */
		$domainModel = $serviceManager->get('domainModel');

		if ($domains = $this->getRequest()->getPost('domains')) {
			foreach ($domains as $domainId) {
				if (!$domain = $domainModel->fetch(['er_id' => $domainId])) {
					throw new ClassNotFoundException('Домен не найден (' . $domainId . ')');
				}
				$domainModel->delDomain(['er_id' => $domain['er_id']]);
			}
		}
		$this->flashMessenger()->addInfoMessage(DomainUpdate::ERROR_MESSAGE);


		$domainModel->setController($this->controller);
		return $this->redirect()->toRoute('lists', ['controller' => $domainModel->getController()]);
	}

	/**
	 * Create domain/domains
	 * @return array|\Zend\Http\Response
	 */
	public function createAction()
	{
		$form = new Domain();
		$domains = new Domains();
		$form->bind($domains);

		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());

			if ($form->isValid()) {

				$serviceManager = $this->getServiceLocator();
				/** @var DomainModel $domainModel */
				$domainModel = $serviceManager->get('domainModel');

				/** @var AuthenticationService $authService */
				$authService = $serviceManager->get('authService');
				$client = $authService->getIdentity();

				foreach ($domains->getDomains() as $domain) {
					$domainModel->addDomain(['resource' => $domain->getResource(), 'description' => $domain->getDescription(),
						'clientid' => (int)$client['id'], 'type_block' => $this->type]);

				}
				$this->flashMessenger()->addSuccessMessage(Domain::SUCCESS_MESSAGE);
				return $this->redirect()->toRoute('lists/' . $this->controller, ['controller' => $this->controller]);
			}
		}

		return array(
			'form' => $form,
		);
	}

	/**
	 * Update domain
	 * @return \Zend\Http\Response|ViewModel
	 */
	public function updateAction()
	{
		$serviceManager = $this->getServiceLocator();
		/** @var DomainModel $domainModel */
		$domainModel = $serviceManager->get('domainModel');

		$domainId = $this->params()->fromRoute('id');
		if (!$domain = $domainModel->fetch(['er_id' => $domainId])) {
			throw new ClassNotFoundException('Домен не найден (' . $domain['er_id'] . ')');
		}

		$form = new DomainUpdate();

		/** @var Request $request */
		$request = $this->getRequest();
		if ($request->isPost()) {

			$form->setData($request->getPost());
			$form->setInputFilter($form->inputFilter());

			if ($form->isValid()) {
				$formData = $form->getData();
				$dataToSave = [
					'resource' => $formData['resource'],
					'description' => $formData['description'],
				];

				$domainModel->update($dataToSave, ['er_id' => $domain['er_id']]);
				$this->flashMessenger()->addSuccessMessage(DomainUpdate::SUCCESS_MESSAGE);

				return $this->redirect()->toRoute('lists/' . $this->controller, ['controller' => $this->controller]);

			}
		} else {
			$form->setData($domain);
		}

		return new ViewModel(['form' => $form, 'er_id' => $domainId]);
	}

	/**
	 * Remove domain
	 * @return \Zend\Http\Response
	 */
	public function removeOneAction()
	{
		$serviceManager = $this->getServiceLocator();
		/** @var DomainModel $domainModel */
		$domainModel = $serviceManager->get('domainModel');
		$domainId = $this->params()->fromRoute('id');

		if (!$domain = $domainModel->fetch(['er_id' => $domainId])) {
			throw new ClassNotFoundException('Домен не найден (' . $domainId . ')');
		}
		$domainModel->delDomain(['er_id' => $domain['er_id']]);
		$this->flashMessenger()->addInfoMessage(DomainUpdate::INFO_MESSAGE);

		$domainModel->setController($this->controller);
		return $this->redirect()->toRoute('lists/' . $domainModel->getController(), ['controller' => $domainModel->getController()]);
	}

	/**
	 * Create domain/domains from *.txt file
	 * @return array|\Zend\Http\Response
	 */
	public function importAction()
	{
		$serviceManager = $this->getServiceLocator();
		$form = new Import();
		$form->setInputFilter($form->inputFilter());

		$request = $this->getRequest();
		if ($request->isPost()) {
			// Make certain to merge the files info!
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);

			$form->setData($post);
			if ($form->isValid()) {
				$data = $form->getData();
				$parser = new ImportDomain();
				if ($domainResources = $parser->parse($data['file']['tmp_name'])) {
					/** @var DomainModel $domainModel */
					$domainModel = $serviceManager->get('domainModel');

					/** @var AuthenticationService $authService */
					$authService = $serviceManager->get('authService');
					$client = $authService->getIdentity();

					foreach ($domainResources as $domainResource) {
						$domainModel->addDomain([
							'resource' => $domainResource,
							'clientid' => (int)$client['id'],
							'description' => $serviceManager->get('config')['domainDescription'],
							'type_block' => $this->type,
						]);
					}
					$this->flashMessenger()->addSuccessMessage(DomainUpdate::SUCCESS_MESSAGE);
					return $this->redirect()->toRoute('lists/' . $this->controller, ['controller' => $this->controller]);
				}
				$form->get('file')->setMessages($parser->getErrors());
			}
		}

		return array('form' => $form);
	}
}