<?php
/**
 * Plugin Name: Litteraly WordPress
 * Plugin URI: http://wordpress.org/extend/plugins/literally-wordpress/
 * Description: This plugin make your WordPress post payable. Registered users can buy your post via PayPal. You can provide several ways to reward their buying. Add rights to download private file, to accesss private post and so on.
 * Author: Takahashi Fumiki<takahashi.fumiki@hametuha.co.jp>
 * Version: 0.9.1.2
 * Author URI: http://takahashifumiki.com
 * Text Domain: literally-wordpress
 * Domain Path: /language/
 * 
 * 
 * @Package WordPress
 * License: GPLv2
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; version 2 of the License.
 * 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details. 
 * 
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the Free Software 
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
 */

//Check requirements.
if(version_compare(PHP_VERSION, '5.0') >= 0 && function_exists('curl_init')){
		
	//Load class files.
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."literally-wordpress-core.php";
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR.'literally-wordpress-statics.php';
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."paypal_statics.php";
	//Subclass
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."literally-wordpress-common.php";
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."literally-wordpress-post.php";
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."literally-wordpress-form.php";
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."literally-wordpress-notifier.php";
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."literally-wordpress-subscription.php";
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."literally-wordpress-reward.php";
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."literally-wordpress-event.php";
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."literally-wordpress-ios.php";
	
	/**
	 * Instance of Literally_WordPress
	 *
	 * @var Literally_WordPress
	 */
	$lwp = new Literally_WordPress();

	//Load user functions.
	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."functions.php";
	
	//For poedit scraping. It won't be executed.
	if(false){
		$lwp->_('Literally WordPress is activated but is not available. This plugin needs PHP version 5<. Your PHP version is %1$s.');
		$lwp->_(' Furthermore, this plugin needs cURL module.');
		$lwp->_(' Please contact to your server administrator to change server configuration.');
	}
	
}else{
	
	load_plugin_textdomain('literally-wordpress', false, basename(__FILE__).DIRECTORY_SEPARATOR."language");
	$error_msg = sprintf(__('Literally WordPress is activated but is not available. This plugin needs PHP version 5<. Your PHP version is %1$s.', 'literally-wordpress'), phpversion());
	if(!function_exists('curl_init')){
		$error_msg .= __(' Furthermore, this plugin needs cURL module.', 'literally-wordpress');
	}
	$error_msg .= __(' Please contact to your server administrator to change server configuration.');
	add_action('admin_notices', create_function('', 'echo "<div id=\"message\" class=\"error\"><p><strong>'.$error_msg.'</strong></p></div>"; '));
}