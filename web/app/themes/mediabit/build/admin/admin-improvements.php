<?php 
function custom_admin_script() {
    wp_enqueue_script('custom-admin-js', get_template_directory_uri() . '/build/admin/admin-custom.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'custom_admin_script');

function custom_admin_style() {
    wp_enqueue_style('custom-admin-css', get_template_directory_uri() . '/build/admin/admin-style.css');
}
add_action('admin_enqueue_scripts', 'custom_admin_style');
