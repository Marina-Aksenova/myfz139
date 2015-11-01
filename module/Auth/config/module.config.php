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
			'auth' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/auth/[:controller[/:action]]',
					'defaults' => array(
						'__NAMESPACE__' => 'Auth\Controller',
						'controller'    => 'Index',
						'action'        => 'index',
					),
					'constraints' => [
						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
					]
				),
			),
			'login' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/login',
					'defaults' => array(
						'controller'    => 'Auth\Controller\Index',
						'action'        => 'login',
					),
				),
			),
			'logout' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/logout',
					'defaults' => array(
						'controller'    => 'Auth\Controller\Index',
						'action'        => 'logout',
					),
				),
			),
		),
	),
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Index' => 'Auth\Controller\IndexController',
		),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

);
