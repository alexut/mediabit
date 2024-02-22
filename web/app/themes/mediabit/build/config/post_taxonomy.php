<?php

function register_custom_taxonomy($taxonomy, $singular_label, $plural_label, $post_type, $args = []) {
    $labels = array(
        'name' => $plural_label,
        'singular_name' => $singular_label,
        'search_items' => 'Search ' . $plural_label,
        'popular_items' => 'Popular ' . $plural_label,
        'all_items' => 'All ' . $plural_label,
        'parent_item' => 'Parent ' . $singular_label,
        'parent_item_colon' => 'Parent ' . $singular_label . ':',
        'edit_item' => 'Edit ' . $singular_label,
        'view_item' => 'View ' . $singular_label,
        'update_item' => 'Update ' . $singular_label,
        'add_new_item' => 'Add New ' . $singular_label,
        'new_item_name' => 'New ' . $singular_label . ' Name',
        'separate_items_with_commas' => 'Separate ' . $plural_label . ' with commas',
        'add_or_remove_items' => 'Add or remove ' . $plural_label,
        'choose_from_most_used' => 'Choose from the most used ' . $plural_label,
        'not_found' => 'No ' . $plural_label . ' found',
        'no_terms' => 'No ' . $plural_label,
        'items_list_navigation' => $plural_label . ' list navigation',
        'items_list' => $plural_label . ' list',
        'back_to_items' => '&larr; Back to ' . $plural_label,
    );

    $default_args = array(
        'labels' => $labels,
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => true,
        'show_tagcloud' => true,
        'show_in_quick_edit' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => $taxonomy),
    );

    $args = wp_parse_args($args, $default_args);

    register_taxonomy($taxonomy, $post_type, $args);
}

add_action('init', function() {
    register_custom_taxonomy('location', 'Location', 'Locations', 'event');
    register_custom_taxonomy('position', 'Position', 'Positions', 'team-members');
    register_custom_taxonomy('testimonial_type', 'Testimonial Type', 'Testimonial Types', 'testimonial');
});