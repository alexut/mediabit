<?php namespace Mediabit\Shortcodes;

use Mediabit\Shortcodes\Wrapper;

class SocialLinks {
    public static function init() {
        Wrapper::register('social_links', [self::class, 'render_shortcode']);
    }

    public static function render_shortcode($atts) {
        $atts = shortcode_atts([
            'class' => '',
            'list'  => 'facebook,instagram,twitter,linkedin',
        ], $atts, 'social_links');

        $list_items = explode(",", $atts['list']);

        $result = '<div class="social-links ' . $atts['class'] . '">';
        foreach ($list_items as $list_item) {
            $stringlink = $list_item . '_link';
            $link = get_field($stringlink, 'option');
            if ($link) {
                $result .= '<a href="' . $link . '" target="_blank" class="mx-1"><i class="bi bi-' . $list_item . '"></i></a>';
            }
        }
        $result .= '</div>';

        return $result;
    }

}

SocialLinks::init();
