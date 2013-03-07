<?php
/**
 * Plugin Name: Canvas Addons
 * Plugin URI: http://stuartduff.com/
 * Description: Adds some advanced styling features to WooThemes Canvas theme.
 * Author: Stuart Duff
 * Version: 1.0
 * Author URI: http://stuartduff.com/
 *
 * @package WordPress
 * @subpackage Canvas_Advanced_Addons
 * @author Stuart
 * @since 1.0.0
 */

require_once( 'classes/class-canvas-advanced-addons.php' );
require_once( 'classes/class-updater.php' );

global $canvas_advanced_addons;
$canvas_advanced_addons = new Canvas_Advanced_Addons( __FILE__ );

		if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
		    $config = array(
		        'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
		        'proper_folder_name' => 'canvas-advanced-addons', // this is the name of the folder your plugin lives in
		        'api_url' => 'https://api.github.com/repos/stuartduff/canvas-advanced-addons', // the github API url of your github repo
		        'raw_url' => 'https://raw.github.com/stuartduff/canvas-advanced-addons/master', // the github raw url of your github repo
		        'github_url' => 'https://github.com/stuartduff/canvas-advanced-addons', // the github url of your github repo
		        'zip_url' => 'https://github.com/stuartduff/canvas-advanced-addons/zipball/master', // the zip url of the github repo
		        'sslverify' => true, // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
		        'requires' => '3.5', // which version of WordPress does your plugin require?
		        'tested' => '3.5', // which version of WordPress is your plugin tested up to?
		        'readme' => 'README.md', // which file to use as the readme for the version number
		        'access_token' => '', // Access private repositories by authorizing under Appearance > Github Updates when this example plugin is installed
		    );
		    new WP_GitHub_Updater($config);
		}

?>