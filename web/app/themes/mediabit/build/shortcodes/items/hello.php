<?php
// Demo shortcode.

namespace Mediabit\Shortcodes;

use Mediabit\Shortcodes\Wrapper;

class Hello {
    public static function init() {
        Wrapper::register('hello', [__CLASS__, 'render']);
    }

    public static function render($atts, $content = null) {
        $atts = shortcode_atts(
            [
                'name' => 'World',
            ],
            $atts,
            'hello'
        );

        return '<p>Hello, ' . esc_html($atts['name']) . '!</p>';
    }
}

// Initialize the hello shortcode.
Hello::init();