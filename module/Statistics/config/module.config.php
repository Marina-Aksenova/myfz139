<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'statistics' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/statistics',
                    'defaults' => array(),
                ),
                'may_terminate' => true,
				'child_routes' => array(
					'cl-statistic' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => '/cl-statistic[/:action]',
							'constraints' => array(
								'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
							),
							'defaults' => array(
								'controller' => 'Statistics\Controller\ClientsStatistic',
								'action' => 'index',
							),
						),
					),
					'gr-statistic' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => '/gr-statistic[/:action]',
							'constraints' => array(
								'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
							),
							'defaults' => array(
								'controller' => 'Statistics\Controller\GroupStatistic',
								'action' => 'index',
							),
						),
					),
					'cl-statistic-often' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => '/cl-statistic-often[/:action]',
							'constraints' => array(
								'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
							),
							'defaults' => array(
								'controller' => 'Statistics\Controller\ClientStatisticOften',
								'action' => 'index',
							),
						),
					),
                    'gr-statistic-often' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/gr-statistic-often[/:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Statistics\Controller\GroupStatisticOften',
                                'action' => 'index',
                            ),
                        ),
                    ),
				),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Statistics\Controller\Index' => 'Statistics\Controller\IndexController',
            'Statistics\Controller\Statistic' => 'Statistics\Controller\StatisticController',
            'Statistics\Controller\ClientsStatistic' => 'Statistics\Controller\ClientsStatisticController',
            'Statistics\Controller\GroupStatistic' => 'Statistics\Controller\GroupStatisticController',
            'Statistics\Controller\GroupStatisticOften' => 'Statistics\Controller\GroupStatisticOftenController',
            'Statistics\Controller\ClientStatisticOften' => 'Statistics\Controller\ClientStatisticOftenController',
        ),
    ),
    'translator' => array(
        'locale' => 'ru_RU',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

);
