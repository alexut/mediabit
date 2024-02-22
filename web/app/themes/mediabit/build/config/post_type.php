<?php

function find_icon_for_post_type($post_type_name, $icon_names) {
    foreach ($icon_names as $icon => $post_type_names) {
        if (in_array($post_type_name, $post_type_names)) {
            return $icon;
        }
    }
    // Default icon if no suitable icon is found
    return 'dashicons-admin-post';
}

function register_custom_post_type($post_type, $singular_label, $plural_label, $args = []) {
    $labels = array(
        'name' => $plural_label,
        'singular_name' => $singular_label,
        'add_new' => 'Add New',
        'add_new_item' => 'Add New ' . $singular_label,
        'edit_item' => 'Edit ' . $singular_label,
        'new_item' => 'New ' . $singular_label,
        'view_item' => 'View ' . $singular_label,
        'view_items' => 'View ' . $plural_label,
        'search_items' => 'Search ' . $plural_label,
        'not_found' => 'No ' . $plural_label . ' found',
        'not_found_in_trash' => 'No ' . $plural_label . ' found in Trash',
        'parent_item_colon' => 'Parent ' . $singular_label . ':',
        'all_items' => 'All ' . $plural_label,
        'archives' => $singular_label . ' Archives',
        'attributes' => $singular_label . ' Attributes',
        'insert_into_item' => 'Insert into ' . $singular_label,
        'uploaded_to_this_item' => 'Uploaded to this ' . $singular_label,
        'featured_image' => $singular_label . ' Image',
        'set_featured_image' =>  'Set ' . $singular_label . ' image',
        'remove_featured_image' =>  'Remove ' . $singular_label . ' image',
        'use_featured_image' =>  'Use as ' . $singular_label . ' image',
        'filter_items_list' => 'Filter ' . $plural_label . ' list',
        'items_list_navigation' => $plural_label . ' list navigation',
        'items_list' => $plural_label . ' list',
        'item_published' => $singular_label . ' published.',
        'item_published_privately' => $singular_label . ' published privately.',
        'item_reverted_to_draft' => $singular_label . ' reverted to draft.',
        'item_scheduled' => $singular_label . ' scheduled.',
        'item_updated' => $singular_label . ' updated.',
    );

    $icon_names = array(
        'dashicons-portfolio' => array('project', 'projects', 'portfolio'),
        'dashicons-calendar' => array('event', 'events', 'calendar'),
        'dashicons-testimonial' => array('testimonial', 'testimonials', 'quote'),
        'dashicons-cart' => array('product', 'products', 'shopping'),
        'dashicons-hammer' => array('service', 'services', 'tool'),
        'dashicons-groups' => array('team-members','team_member', 'team_members', 'staff'),
        'dashicons-format-gallery' => array('gallery', 'galleries', 'photos'),
        // Add more as needed
    );

    $default_args = array(
        'labels' => $labels,
        'public' => true,
        'supports' => array( 'title', 'editor', 'thumbnail'),
        'menu_icon' => find_icon_for_post_type($post_type, $icon_names),
    );

    $args = wp_parse_args($args, $default_args);

    register_post_type($post_type, $args);
}

add_action('init', function() {

    $private_args = array(
        'public' => true,
        'has_archive' => false,
        'publicly_queryable' => false,
        'supports' => array( 'title', 'editor', 'thumbnail'),
    );

    register_custom_post_type('event', 'Event', 'Events');
    register_custom_post_type('team-members', 'Member', 'Team Members', $private_args);
    register_custom_post_type('testimonial', 'Testimonial', 'Testimonials', $private_args);
});