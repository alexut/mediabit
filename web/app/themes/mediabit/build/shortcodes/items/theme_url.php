<?php
namespace Mediabit\Shortcodes;

use Mediabit\Shortcodes\Wrapper;

class ThemeUrl {
    public static function init() {
        Wrapper::register('theme_url', [__CLASS__, 'render']);
    }

    public static function render($atts, $content = null) {
        $theme = ( is_child_theme() ? get_stylesheet_directory_uri() : get_template_directory_uri() );
        return $theme;
    }
}

// Initialize the theme_url shortcode.
ThemeUrl::init();
