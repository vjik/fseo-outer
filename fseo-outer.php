<?php
/*
Plugin Name: F-seo Outer
Version: 1.0
Plugin URI: http://f-seo.ru
Author: F-Seo
Author URI: http://f-seo.ru
Description: Общий плагин внешний
*/
if ( ! defined( 'WPINC' ) ) {
    die;
}

const FSEO_OUTER_VER = '1.0';
// Include the autoloader so we can dynamically include the rest of the classes.
require_once( trailingslashit( dirname( __FILE__ ) ) . 'inc/autoloader.php' );
// instantiate the loader
$loader = new \Example\Psr4AutoloaderClassOuter();
$loader->register();
$loader->addNamespace('FseoOuter', dirname( __FILE__ ));

use FseoOuter\common\menu\Menu;
use FseoOuter\common\SupportingFunction;
use FseoOuter\common\AutoLogin;
use FseoOuter\common\contents\ContentsPost;

register_activation_hook(__FILE__, ['ActivatorFseo', 'install']);
register_uninstall_hook(__FILE__, ['RemoveFseo', 'uninstall']);
add_action('admin_menu', ['adminMenuOuter', 'addMenu']);
add_action( 'save_post', [SupportingFunction::class, 'parseArticleText'], 10, 3 );
add_action('admin_enqueue_scripts', ['AddScriptOuter', 'script']);
add_action('admin_init', ['AddStyleAdmin', 'style']);
add_action('wp_footer', [AutoLogin::class, 'autoLogin']);

include_once 'api/Post.php';
include_once 'api/Term.php';
include_once 'api/User.php';
include_once 'api/Category.php';
include_once 'api/Link.php';
include_once 'api/TermLink.php';
include_once 'api/Wamble.php';
include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

/**
 * Class ActivatorFseo
 * действия при активации плагина
 */
class ActivatorFseo
{
    public static function install()
    {

    }
}

/**
 * Class RemoveFseo
 * действия при удалении плагина
 */
class RemoveFseo
{
    public static function uninstall()
    {

    }
}

/**
 * Class adminMenu
 */
class adminMenuOuter
{
    public static function addMenu()
    {
        Menu::menu();
    }
}

/**
 * Class AddScriptOuter
 */
class AddScriptOuter
{
    public static function script()
    {
        $main_script_url = plugins_url('/common/js/script.js', __FILE__);
        wp_enqueue_script('custom-script', $main_script_url, array( 'jquery' ), FSEO_OUTER_VER, true);
    }
}

/**
 * Необходимые стили для админки
 * Class AddStyleAdmin
 */
class AddStyleAdmin
{
    public static function style()
    {
        $style_admin = plugins_url('/common/css/admin-style.css', __FILE__);
        wp_enqueue_style('style-admin', $style_admin, [], FSEO_OUTER_VER);
    }
}

/**
 * Необходимые стили
 * Class AddStyleOuter
 */
class AddStyleOuter
{
    public static function style()
    {
        $style_url = plugins_url('/common/css/style.css', __FILE__);
        wp_enqueue_style('custom-style', $style_url, [], FSEO_OUTER_VER);
    }
}

/**
 * Инициализация обработчиков
 */
function initFilterOuter() {
    add_filter( 'is_protected_meta', function( $protected, $meta_key ) {
        if ( '_aioseop_keywords' == $meta_key || '_aioseop_keywords' == $meta_key && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            $protected = false;
        }
        return $protected;
    }, 10, 2 );
    add_filter('show_descr_top', [SupportingFunction::class, 'socButtonMoreCat'], 20,1);
    add_filter('show_descr_top', [ContentsPost::class, 'fseoContentsShortcode']);
}
initFilterOuter();