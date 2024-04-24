<?php
    // Exit if accessed directly.
    defined( 'ABSPATH' ) || exit;

// initialize contact.js
add_action( 'wpcf7_enqueue_scripts', 'cf7_contact_js' );

function cf7_contact_js() {
    wp_enqueue_script( 'cf7-custom-init', get_stylesheet_directory_uri() . '/assets/js/contact.js', array('contact-form-7' ), null, true );
}

// custom tag
    add_action( 'wpcf7_init', 'custom_add_form_tag_range_slider' );


function custom_add_form_tag_range_slider() {
    wpcf7_add_form_tag( 'range_slider', 'custom_range_slider_form_tag_handler' ); // "range_slider" is the type of the form-tag
}

function custom_range_slider_form_tag_handler( $tag ) {
    $tag = new WPCF7_FormTag( $tag );

  // Default attributes
  $defaults = array(
    'min'   => 0,
    'max'   => 10,
    'step'  => 1,  // Adjusted to a more common default step value
    'value' => 5,  // Adjusted to a more common default starting value
);


   // Parse dynamic attributes, ensure correct fallback to defaults
   $min = $tag->get_option( 'min', 'int', true ) ?: $defaults['min'];
   $max = $tag->get_option( 'max', 'int', true ) ?: $defaults['max'];
   $step = $tag->get_option( 'step', 'int', true ) ?: $defaults['step']; // Ensure type consistency
   $value = $tag->get_option( 'value', 'int', true ) ?: $defaults['value']; // Ensure type consistency


    // var_dump($min, $max, $step, $value);
    
  // Generate the input HTML
  $html = '<input type="range" class="form-range" name="' . $tag->name . '" ';
  $html .= 'min="' . esc_attr( $min ) . '" max="' . esc_attr( $max ) . '" step="' . esc_attr( $step ) . '" ';
  $html .= 'value="' . esc_attr( $value ) . '" id="energy-bill-range">';

  return $html;
}

// color picker

add_action( 'wpcf7_init', 'custom_add_form_tag_color_picker' );

function custom_add_form_tag_color_picker() {
    wpcf7_add_form_tag( 'color_picker', 'custom_color_picker_form_tag_handler' ); // "color_picker" is the type of the form-tag
}

function custom_color_picker_form_tag_handler( $tag ) {
    $tag = new WPCF7_FormTag( $tag );

    // Default color
    $default_color = '#563d7c';

    // Parse dynamic attributes
    $color = $tag->get_option( 'color', 'color', true ) ?: $default_color;

    // Generate the input HTML
    $html = '<input type="color" class="form-control form-control-color" name="' . $tag->name . '" ';
    $html .= 'value="' . esc_attr( $color ) . '" id="' . esc_attr( $tag->name ) . '" title="Choose your color">';

    return $html;
}

//choices
// Register custom CF7 tag
add_action( 'wpcf7_init', 'custom_add_form_tag_image_choice' );

function custom_add_form_tag_image_choice() {
    wpcf7_add_form_tag( array( 'choiceimage' ), 'custom_choice_image_form_tag_handler', true );
}


function custom_choice_image_form_tag_handler($tag) {
    if (empty($tag->name)) {
        return '';
    }

    $theme_images_url = get_template_directory_uri() . '/assets/images/';

    // Prepare the data array for JavaScript
    $options_data = [];

    // Build the options data array
    foreach ($tag->values as $option) {
        if (strpos($option, '--') !== false) {
            list($image, $text) = explode('--', $option, 2);
            $image_url = $theme_images_url . trim($image);
            $options_data[] = ['label' => trim($text), 'value' => trim($text), 'image' => esc_url($image_url)];
        } else {
            $options_data[] = ['label' => trim($option), 'value' => trim($option), 'image' => ''];
        }
    }

    // Create an input field as a placeholder for the custom dropdown
    $html = '<select id="' . esc_attr($tag->name) . '" name="' . esc_attr($tag->name) . '" class="wpcf7-form-control wpcf7-text wpcf7-choice-image" data-options=\'' . json_encode($options_data) . '\'></select>';

    return $html;
}

// Register custom CF7 tag for choice (single and multi-select)
add_action('wpcf7_init', 'custom_add_form_tag_choice');
function custom_add_form_tag_choice() {
    wpcf7_add_form_tag(array('choice'), 'custom_choice_form_tag_handler', true);
}

function custom_choice_form_tag_handler($tag) {
    if (empty($tag->name)) {
        return '';
    }

    // Start building the select element
    $html = '<select id="' . esc_attr($tag->name) . '" name="' . esc_attr($tag->name) . '"';
    $html .= ' class="wpcf7-form-control wpcf7-choice">';

    // Add options
    foreach ($tag->values as $value) {
        // Check if the value contains the delimiter '|'
        if (strpos($value, '|') !== false) {
            // If so, split into value and text
            list($option_value, $option_text) = explode('|', $value, 2);
        } else {
            // If not, use the same value for both the value and text
            $option_value = $value;
            $option_text = $value;
        }
        $html .= sprintf('<option value="%s">%s</option>', esc_attr($option_value), esc_html($option_text));
    }

    $html .= '</select>';

    return $html;
}


add_action( 'wpcf7_init', 'custom_add_form_tag_multichoice' );

function custom_add_form_tag_multichoice() {
    wpcf7_add_form_tag( array( 'multichoice' ), 'custom_multichoice_form_tag_handler', true );
}

function custom_multichoice_form_tag_handler( $tag ) {
    if ( empty( $tag->name ) ) {
        return '';
    }

    // No need to recreate $tag as WPCF7_FormTag here since it should already be passed as one.

    $html = '<select id="' . esc_attr( $tag->name ) . '" name="' . esc_attr( $tag->name ) . '[]" multiple="multiple" class="wpcf7-form-control wpcf7-multichoice">';

    foreach ( $tag->values as $option ) {
        $parts = explode( '|', $option, 2 );
        $value = $parts[0];
        $label = isset($parts[1]) ? $parts[1] : $value; // Use $value as label if $label is not set
        $html .= sprintf( '<option value="%s">%s</option>', esc_attr( $value ), esc_html( $label ) );
    }

    $html .= '</select>';

    return $html;
}
