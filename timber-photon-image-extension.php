<?php
/*
Plugin Name: Timber Jetpack Photon Image Extension
Plugin URI: http://slimndap.com
Description: Make the Timber plugin work with Jetpack's Photon. Once installed, all TimberImages will use Photon as a CDN and for image manipulation (eg. resize).
Author: Jeroen Schmit
Version: 0.4
Author URI: http://slimndap.com
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

require_once( plugin_dir_path( __FILE__ ) . 'src/TimberPhoton.php' );

$admin_notes = [];

/**
 * Check if Timber and Jetpack are installed and activated.
 * Check if Photon is activated
 *
 * @return bool
 */
function system_ready()
{
    // Is Timber installed and activated?
    if (!class_exists('Timber')) {
        $admin_notes[] = 'timber';
        add_action('admin_notices', 'admin_notices');

        return false;
    }

    // Determine if Jetpack is installed and can generate photon URLs.
    if (!class_exists('Jetpack') || !method_exists('Jetpack', 'get_active_modules') || !in_array('photon', Jetpack::get_active_modules())) {
        $admin_notes[] = 'photon';
        add_action('admin_notices', 'admin_notices');

        return false;
    }

    return true;
}


function admin_notices()
{
    if (!empty($admin_notes)) {

        echo '<div class="error"><p>';

        if (in_array('timber', $admin_notes)) {
            _e('Timber with Jetpack Photon requires the Timber plugin to be installed and activated. <a href="http://jarednova.github.io/timber/">Get it here</a>.');
        }

        if (in_array('photon', $admin_notes)) {
            _e('Timber with Jetpack Photon requires the Jetpack plugin to be installed with Photon activated.');
        }

        echo '</p></div>';
    }
}

function timber_photon_image_extension_start()
{
    if(system_ready()) {
        $timber_photon = new TimberPhoton();

        $timber_photon->initialize();
    }
}

timber_photon_image_extension_start();