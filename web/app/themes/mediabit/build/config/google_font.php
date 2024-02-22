<?php

function enqueue_google_fonts() {
    $google_fonts_url = 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap';
    wp_enqueue_style('google-fonts', $google_fonts_url, [], null);
}

add_action('wp_enqueue_scripts', 'enqueue_google_fonts');

function theme_google_fonts_resource_hints($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        $urls[] = [
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => true,
        ];
        $urls[] = [
            'href' => 'https://fonts.googleapis.com',
            'crossorigin' => true,
        ];
    }
    return $urls;
}

add_filter('wp_resource_hints', 'theme_google_fonts_resource_hints', 10, 2);