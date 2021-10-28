<?php
/**
 * Plugin Name: Google Drive Sharing
 * Plugin URI: https://www.weerayutteja.com
 * Author: Weerayut Teja
 * Author URI: https://www.weerayutteja.com
 * Version: 1.0
 * Description: Create Downloadable Google Drive Link, Generate post thumbnail from PDF cover.
 */

 require('util.php');

 if( is_admin() ) {
    require_once('metabox.php');
 } else {
    require_once('shortcode.php');
 }