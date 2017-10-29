<?php
/*
Plugin Name: Shop Calendar
Plugin URI: https://github.com/akrmd/wp-shop-calendar
Description: A store indicates the state of the opening or the holiday by a calendar widget.
Version: 1
Author: shiba
Author URI: http://wp.akirumade.com/
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: wp-shop-calendar
Domain Path: /i18n/languages
 */

if (!defined('WPSHOPCAL_PLUGIN_NAME')) {
    define('WPSHOPCAL_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
}

if (!defined('WPSHOPCAL_PLUGIN_DIR')) {
    define('WPSHOPCAL_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WPSHOPCAL_PLUGIN_NAME);
}

new wp_shop_calendar_init;
class wp_shop_calendar_init
{

    public function __construct()
    {
        add_action('plugins_loaded', array(__CLASS__, 'load_textdomain'));
    }
    public function load_textdomain()
    {
        load_plugin_textdomain('wp_shop_calendar', false, dirname(plugin_basename(__FILE__)) . '/i18n/languages/');
    }

}

function wp_shop_calendar_today_status($atts)
{
    extract(shortcode_atts(array(
        'format' => 'm/d',
    ), $atts));
    $calendar = new wp_shop_calendar();
    return $calendar->today($format);
    return date($format, time());
}
add_shortcode('today_status', 'wp_shop_calendar_today_status');
function wp_shop_calendar_private_holiday_list($atts)
{
    extract(shortcode_atts(array(
        'format' => 'm/d',
    ), $atts));

    $calendar = new wp_shop_calendar();
    return $calendar->private_holiday_list($format);
}
add_shortcode('private_holiday_list', 'wp_shop_calendar_private_holiday_list');

require_once WPSHOPCAL_PLUGIN_DIR . '/include/class-core.php';
require_once WPSHOPCAL_PLUGIN_DIR . '/include/class-options.php';
require_once WPSHOPCAL_PLUGIN_DIR . '/include/class-widget.php';
