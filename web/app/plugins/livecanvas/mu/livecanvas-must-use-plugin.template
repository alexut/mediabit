<?php
/*
Plugin Name: LiveCanvas Compatibility Filters Plugin
Description: A micro helper plugin, useful to guarantee compatibility with aggressive performance optimization  plugins such as caching, EWWW, etc...
Version: 1.1.2
Author: The LiveCanvas Team
Author URI: https://www.livecanvas.com
*/

add_filter("option_active_plugins", function ($plugins) {

    $lc_settings = get_option('lc_settings');
	$lc_settings = apply_filters('lc_settings', $lc_settings);

    //loading a livecanvas page, disable incompatible plugins
    if ((isset($_GET["lc_page_editing_mode"]) || isset($_GET["lc_action_launch_editing"])) && !isset($lc_settings['disable-mu-plugin'])) {

        $lc_plugin_dir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'livecanvas';
        $lc_filter_list_file = $lc_plugin_dir . DIRECTORY_SEPARATOR . 'mu' . DIRECTORY_SEPARATOR . 'livecanvas-disabled-plugins-list.txt';

        //default and known incompatible plugins
        $targets = [
            "litespeed-cache/litespeed-cache.php",
            //add new ones in txt file
        ];

        //get updated list if any
        if (file_exists($lc_filter_list_file)) {
            $targets = array_map('trim', array_unique(array_merge($targets, array_filter(file($lc_filter_list_file)))));
        }

        foreach ($targets as $target) {
            $foundKey = array_search($target, $plugins, true);
            if ($foundKey !== null && gettype($foundKey) != 'boolean') {
                unset($plugins[$foundKey]);
            }
        }
    }

    return array_values($plugins);
});


// SAME FOR MULTISITE-ENABLED PLUGINS
add_filter("site_option_active_sitewide_plugins", function ($plugins) {

    if (!is_multisite()) return $plugins;

    //if loading LC editor or page is being edited by LC, disable incompatible plugins
    if (isset($_GET["lc_page_editing_mode"]) || isset($_GET["lc_action_launch_editing"])  ) {

        //print_r($plugins); echo "<br><br>"; //for a quick debug

        //default and known incompatible plugins
        $plugins_to_disable = [
            "ewww-image-optimizer/ewww-image-optimizer.php",
            "litespeed-cache/litespeed-cache.php",
            //add new ones in txt file
        ];

        $lc_plugin_dir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'livecanvas';
        
        $lc_filter_list_file = $lc_plugin_dir . DIRECTORY_SEPARATOR . 'mu' . DIRECTORY_SEPARATOR . 'livecanvas-disabled-plugins-list.txt';

        //get updated list from file if available, instead of default one
        if (file_exists($lc_filter_list_file)) { 
            $plugins_to_disable = array_map('trim', array_unique(array_merge($plugins_to_disable, array_filter(file($lc_filter_list_file)))));
        } 

        foreach($plugins as $plugin_name => $plugin_value ){
            if(in_array($plugin_name, $plugins_to_disable)) unset($plugins[$plugin_name]);
        }

        //print_r($plugins); wp_die(); //for a quick debug
    }

    return $plugins;
});
