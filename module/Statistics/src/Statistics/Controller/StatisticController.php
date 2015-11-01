<?php
namespace Statistics\Controller;

use User\Model\ProfileModel;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Sql\Select;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ArrayUtils;
use Statistics\Form\Statistic;
use Statistics\Model\StatisticModel;
use Zend\Db\Sql\Where;

abstract class StatisticController extends AbstractActionController
{
	protected $group = false;

	protected $groupOften = false;

	protected $clientOften = false;

	/**
	 * Build statistic form
	 * @return array
	 * @throws \Exception
	 */
	public function indexAction()
	{
		$err_message = '';
		$statistics = [];

		$serviceManager = $this->getServiceLocator();
		$form = new Statistic();
		/** @var AuthenticationService $authService */
		$authService = $serviceManager->get('authService');
		$day = 60 * 60 * 24;

		/** @var Request $request */
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
			$form->setInputFilter($form->inputFilter());

			if ($form->isValid()) {
				$formData = $form->getData();

				switch ((int)$formData['datePeriod']) {
					case 0:
						$dateCalendarFirst = date("Y-m-d", (time() - $day)) . ' 00:00:00';
						$dateCalendarLast = date("Y-m-d", time()) . ' 23:59:59';
						break;
					case 1:
						$dateCalendarFirst = date("Y-m-d", (time() - $day * 7)) . ' 00:00:00';
						$dateCalendarLast = date("Y-m-d", time()) . ' 23:59:59';
						break;
					case 2:
						$dateCalendarFirst = date("Y-m-d", (time() - $day * 30)) . ' 00:00:00';
						$dateCalendarLast = date("Y-m-d", time()) . ' 23:59:59';
						break;
					case 3:
						$dateCalendarFirst = date("Y-m-d", (time() - $day * 90)) . ' 00:00:00';
						$dateCalendarLast = date("Y-m-d", time()) . ' 23:59:59';
						break;
					case 4:
						$dateCalendarFirst = date("Y-m-d", (time() - $day * 365)) . ' 00:00:00';
						$dateCalendarLast = date("Y-m-d", time()) . ' 23:59:59';
						break;
					default:
						if (!$formData['dateCalendarFirst'] || !$formData['dateCalendarLast']) {
							$err_message = Statistic::ERR_MESSAGE_PERIOD;
						} else if (!$this->validateDate($formData['dateCalendarFirst']) || !$this->validateDate($formData['dateCalendarLast'])) {
							$err_message = Statistic::ERR_MESSAGE_DATE_FORMAT;
						} else {
//							$dateCalendarFirst = $formData['dateCalendarFirst'] . ' 00:00:00';
//							$dateCalendarLast = $formData['dateCalendarLast'] . ' 23:59:59';
							$dateCalendarFirst = date("Y-m-d", strtotime($formData['dateCalendarFirst'])) . ' 00:00:00';
							$dateCalendarLast = date("Y-m-d", strtotime($formData['dateCalendarLast'])) . ' 23:59:59';
							break;
						}
				}
			}
		} else {
			$dateCalendarFirst = date("Y-m-d", (time() - $day * 7)) . ' 00:00:00';
			$dateCalendarLast = date("Y-m-d", time()) . ' 23:59:59';
		}
		/** @var StatisticModel $statisticModel */
		$statisticModel = $serviceManager->get('statisticModel');

		$statisticsRowCount = $serviceManager->get('config')['statisticsRowCount'];

		switch (true) {
			// group statistic
			case $this->group:
				if ($err_message) {
					return array('form' => $form, 'statistics' => $statistics, 'err_message' => $err_message);
				} else {
					$clientId = $authService->getIdentity()['id'];
					/** @var ProfileModel $profileModel */
					$profileModel = $serviceManager->get('profileModel');
					$clientGroupId = $profileModel->getById($clientId, $columnName = 'id')['group_id'];
					$statistics = $statisticModel->getSelectGroup(['group_id' => $clientGroupId, 'date_start' => $dateCalendarFirst,
						'date_end' => $dateCalendarLast, 'limit' => $statisticsRowCount]);
					break;
				}
			// client statistic (often)
			case $this->clientOften:
				$clientId = $authService->getIdentity()['id'];
				$statistics = $statisticModel->getSelectClientOften(['clientid' => $clientId, 'limit' => $statisticsRowCount]);
				break;
			// group statistic (often)
			case $this->groupOften:
				$clientId = $authService->getIdentity()['id'];
				/** @var ProfileModel $profileModel */
				$profileModel = $serviceManager->get('profileModel');
				$clientGroupId = $profileModel->getById($clientId, $columnName = 'id')['group_id'];
				$statistics = $statisticModel->getSelectGroupOften(['group_id' => $clientGroupId, 'limit' => $statisticsRowCount]);
				break;
			// clients statistic
			default:
				if ($err_message) {
					return array('form' => $form, 'statistics' => $statistics, 'err_message' => $err_message);
				} else {
					$clientId = $authService->getIdentity()['id'];
					$statistics = $statisticModel->getSelectDomain(['clientid' => $clientId, 'date_start' => $dateCalendarFirst,
						'date_end' => $dateCalendarLast, 'limit' => $statisticsRowCount]);
					break;
				}
		}

		return array('form' => $form, 'statistics' => $statistics, 'err_message' => $err_message);
	}

	protected function validateDate($date, $format = 'd.m.Y')
	{
		$d = \DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

}