<?php

function theme_enqueue() {

    //  '$handle' => ['$src', '$deps' , $ver, '$in_footer' ]

    $scripts = [
        'vendor-script'      => ['/assets/js/vendor.js','', true],     
        'theme-script'      => ['/assets/js/theme.js', '', true],
        'htmx-script' 		=> ['/assets/js/htmx.min.js','',true],
    ];

    $styles = [
        'theme-style'       => ['/assets/css/theme.css', '', 'all'],
        'theme-font-icon'   => ['//cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css', '', ''],
    ];
    // WP_ENV='development' || WP_ENV='production'

    foreach ($scripts as $k => $v) {
        // if file is cdn
        if (strpos($v[0], '//') !== FALSE) {
            wp_enqueue_script($k, $v[0], $v[1], $fver, $v[2]);
        } else {

            if ( WP_ENV == 'development') {
                $fver = time();
            } else {
                $fver = filemtime(get_stylesheet_directory() . $v[0]);
            }
            wp_enqueue_script($k, get_template_directory_uri() . $v[0], $v[1], $fver, $v[2]);
        }
    }

    foreach ($styles as $k => $v) {
        if (strpos($v[0], '//') !== FALSE) {
            wp_enqueue_style($k, $v[0], $v[1], $fver, $v[2]);
        } else {
            if ( WP_ENV == 'development') {
                $fver = time();
            } else {
                $fver = filemtime(get_stylesheet_directory() . $v[0]);
            }
            wp_enqueue_style($k, get_template_directory_uri() . $v[0], $v[1], $fver, $v[2]);
        }
    }

    wp_deregister_script('wp-mediaelement');
    wp_deregister_style('wp-mediaelement');
    wp_deregister_style('wp-block-library');
    wp_dequeue_style( 'wp-block-library' );

}

add_action('wp_enqueue_scripts', 'theme_enqueue');