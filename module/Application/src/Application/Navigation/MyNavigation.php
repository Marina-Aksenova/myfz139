<?php
namespace Application\Navigation;

use User\Model\ProfileModel;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\Navigation\Exception\InvalidArgumentException;
use Application\Model\MenuModel;
use Application\Module;
use Zend\Authentication\AuthenticationService;

class MyNavigation extends DefaultNavigationFactory
{
	/**
	 * Generate menu
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return array
	 */
	protected function getPages(ServiceLocatorInterface $serviceLocator)
	{
		if (null === $this->pages) {
			/** @var MenuModel $menuModel */
			$menuModel = $serviceLocator->get('menuModel');
			$fetchMenu = $menuModel->fetchAll();

			/** @var AuthenticationService $authService */
			$authService = $serviceLocator->get('authService');
			$clientId = $authService->getIdentity()['id'];
			/** @var ProfileModel $profileModel */
			$profileModel = $serviceLocator->get('profileModel');
			$show_group_stat = (int)$profileModel->getById($clientId, $columnName = 'id')['show_group_stat'];

			$configuration['navigation'][$this->getName()] = array();
			foreach ($fetchMenu as $key => $row) {
				if (in_array($row['route'], ['statistics/gr-statistic', 'statistics/gr-statistic-often']) && $show_group_stat == 0) {
					continue;
				}
				$configuration['navigation'][$this->getName()][$row['name']] = array(
					'label' => $row['label'],
					'route' => $row['route'],
				);
			}

			if (!isset($configuration['navigation'])) throw new InvalidArgumentException('Could not find navigation configuration key');

			if (!isset($configuration['navigation'][$this->getName()])) {
				throw new InvalidArgumentException(sprintf(
					'Failed to find a navigation container by the name "%s"',
					$this->getName()
				));
			}

			$application = $serviceLocator->get('Application');
			$routeMatch  = $application->getMvcEvent()->getRouteMatch();
			$router      = $application->getMvcEvent()->getRouter();
			$pages       = $this->getPagesFromConfig($configuration['navigation'][$this->getName()]);

			$this->pages = $this->injectComponents($pages, $routeMatch, $router);
		}

		return $this->pages;
	}
}