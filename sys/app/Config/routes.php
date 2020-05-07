<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
    Router::connect('/', array('controller' => 'Pages', 'action' => 'index'));

/*
    Router::connect('/admin', array('controller' => 'Dashboard', 'action' => 'index'));
    Router::connect('/admin/', array('controller' => 'Dashboard', 'action' => 'index'));
    Router::connect('/admin/dashboard', array('controller' => 'Dashboard', 'action' => 'index'));
*/

    Router::connect('/estimate/:action/*', array('controller' => 'Estimate'));
    Router::connect('/form/:action/*', array('controller' => 'Form'));
    Router::connect('/item/:action/*', array('controller' => 'Items'));
    Router::connect('/item', array('controller' => 'Items', 'action' => 'index'));

    Router::connect('/member_regist/:action/*', array('controller' => 'MemberRegist'));
    Router::connect('/member_regist', array('controller' => 'MemberRegist', 'action' => 'index'));

    Router::connect('/price/*', array('controller' => 'Pages', 'action' => 'price'));
    Router::connect('/faq/*', array('controller' => 'Pages', 'action' => 'faq'));
    Router::connect('/download/*', array('controller' => 'Pages', 'action' => 'download'));
    Router::connect('/info/sale.html', array('controller' => 'Info', 'action' => 'sale'));
    Router::connect('/info/:action/*', array('controller' => 'Info'));

    Router::connect('/cart/printing', array('controller' => 'Cart', 'action' => 'printing'));

    Router::parseExtensions('json');

/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
    Router::connect('/pages/*', array('controller' => 'Pages'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
    CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
    require CAKE . 'Config' . DS . 'routes.php';
