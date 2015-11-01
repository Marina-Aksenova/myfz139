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
            'lists' => array(
                'type' => 'segment',
                'options' => array(
					'route'    => '/lists/[:controller[/:action[/:id]]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Lists\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
						'id'        	=> 0,
                    ),
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
//						'id'     => '[0-9]+',
					]
                ),
				'may_terminate' => true,
				'child_routes' => array(
					'white-list' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => '/white-list[/:action[/:id[/page/:page]]]',
							'constraints' => array(
								'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[0-9]+',
							),
							'defaults' => array(
								'controller' => 'Lists\Controller\Whitelist',
								'action' => 'index',
								'id' => 0,
							),
						),
					),
					'black-list' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => '/black-list[/:action[/:id[/page/:page]]]',
							'constraints' => array(
								'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[0-9]+',
							),
							'defaults' => array(
								'controller' => 'Lists\Controller\Blacklist',
								'action' => 'index',
								'id' => 0,
							),
						),
					),
				),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Lists\Controller\Index' => 'Lists\Controller\IndexController',
            'Lists\Controller\List' => 'Lists\Controller\ListController',
            'Lists\Controller\WhiteList' => 'Lists\Controller\WhiteListController',
            'Lists\Controller\BlackList' => 'Lists\Controller\BlackListController',
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
