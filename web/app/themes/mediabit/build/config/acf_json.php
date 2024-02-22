<?php
add_action('acf/init', 'my_acf_settings');
function my_acf_settings(){
    acf_update_setting('save_json',  get_stylesheet_directory() . '/fields/acf-json');
    acf_update_setting('load_json', array(
	0 =>  get_stylesheet_directory() . '/fields/acf-json',
    ));
    acf_update_setting('url', get_stylesheet_directory_uri() . '/fields/acf/');
    acf_update_setting('acfe/json_load', array(
        0 => get_stylesheet_directory() . '/fields/acf-json',
    ));
    acf_update_setting('acfe/json_save', get_stylesheet_directory() . '/fields/acf-json');
}
add_filter('acf/settings/load_json', 'my_acf_json_load_point');

function my_acf_json_load_point($paths) {
    // Remove original path (optional)
    unset($paths[0]);

    // Append the path to your acf-json folder in the active theme
    $paths[] = get_stylesheet_directory() . '/fields/acf-json';

    return $paths;
}
