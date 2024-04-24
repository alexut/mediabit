<?php

add_action('wpcf7_before_send_mail', 'cf7_to_custom_post_type_auto', 10, 3);
function cf7_to_custom_post_type_auto($contact_form, &$abort, $submission) {
    $submission_data = $submission->get_posted_data();

    // Create a content structure from the form fields
    $post_content = '';
    foreach ($submission_data as $key => $value) {
        // Skip special fields starting with an underscore
        if (strpos($key, '_') === 0) continue;

        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        
        $post_content .= "<strong>" . esc_html($key) . ":</strong> " . esc_html($value) . "<br />";
    }

    // Set up the post data
    $post_data = array(
        'post_title'    => 'Submission - ' . $contact_form->title(), // Customize the title
        'post_content'  => $post_content,
        'post_status'   => 'publish', // or 'pending', 'draft', etc.
        'post_type'     => 'mesage', // Replace with your custom post type name
    );

    // Insert the post into the database
    $post_id = wp_insert_post($post_data);

    // Optionally, store submitted data as custom fields instead of or in addition to post content
    // foreach ($submission_data as $key => $value) {
    //     if (strpos($key, '_') === 0) continue;
    //     if (is_array($value)) {
    //         $value = implode(', ', $value);
    //     }
    //     update_post_meta($post_id, $key, $value);
    // }
}

