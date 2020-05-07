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

    Router::connect('/admin/dashboard', array('controller' => 'dashboard', 'action' => 'index'));
    Router::connect('/admin/dashboard/:action/*', array('controller' => 'dashboard'));

    Router::connect('/admin/user/:action/*', array('controller' => 'user'));
    Router::connect('/admin/user', array('controller' => 'user', 'action' => 'index'));

    Router::connect('/', array('controller' => 'pages', 'action' => 'index'));
    Router::connect('/admin', array('controller' => 'admin', 'action' => 'index'));
    Router::connect('/admin/:action/*', array('controller' => 'admin'));

/*
    $q = str_replace("url=","",$_SERVER['QUERY_STRING']);
    $arr = explode("/",$q);
    $dir      = $arr[0];

    $ctr_name = "";
    $act_name = "";
    $mode     = "";
    $param    = "";
    $pgnum    = "";

    if(isset($arr[0])) $ctr_name = $arr[0];
    if(isset($arr[1])) $act_name = $arr[1];
    if(isset($arr[2])) $mode     = $arr[2];
    if(isset($arr[3])) $param    = $arr[3];
    if(isset($arr[4])) $pgnum    = $arr[4];

    if($ctr_name === 'root'){
        $url = 'http://'.$_SERVER['HTTP_HOST'].str_replace('/root','',$_SERVER['REQUEST_URI']);
        header('location: '.$url.'/');
        exit;
    }

    // コントローラ存在判定
    $is_exist_method = false;
    $class_name = pascalize($ctr_name);

    $file = APP."Controller".DS.$class_name.'Controller.php';

    if(file_exists($file)){
        App::import('Controller', $class_name);
        $className = $class_name.'Controller';
        $obj = new $className;

        if(empty($act_name)) $act_name = 'index';
        if(method_exists($obj,$act_name)){
            $slug = $ctr_name;
            $url_dir = "/".$dir;
            $action_name = $act_name;
            $p1 = null;
            if(isset($arr[2]))$p1 = $arr[2];
            Router::connect("/*", array('controller' => "$ctr_name", 'action' => "$action_name",$p1,$mode,$param,$pgnum));
            $is_exist_method = true;
        }
    }

    // コントローラ非存在の場合 テンプレート存在判定
    if(!$is_exist_method){

        $path = "index";
        $contr = "Root";
        if(!empty($dir)){
            $page = str_replace(".html","",$ctr_name);
            $page = str_replace(".htm","",$page);
            $page = str_replace(".shtml","",$page);
            $path = $page;
        }

        // ビューにテンプレートがある場合
        $render_name = APP."View".DS."Pages".DS.$path.".ctp";
        if(file_exists($render_name)){
            $viewPath = 'Pages/'.$path;
        }else{
            $viewPath = 'Pages/'.$path;
        }

        Router::connect('/*', array('controller' => 'Root', 'action' => 'page',$viewPath,$mode,$param,$pgnum));

    }
*/

/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
//    Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
//    Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
//    Router::connect('/*', array('controller' => $ctr_name, 'action' => $act_name));

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
