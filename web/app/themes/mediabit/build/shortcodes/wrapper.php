<?php
namespace Mediabit\Shortcodes;

class Wrapper {
    public static function register($tag, $callback) {
        $shortcode = new self($tag, $callback);
        add_shortcode($tag, [$shortcode, 'shortcode_callback']);
    }

    private $tag;
    private $callback;

    private function __construct($tag, $callback) {
        $this->tag = $tag;
        $this->callback = $callback;
    }

    public function shortcode_callback($atts, $content = null) {
        return call_user_func($this->callback, $atts, $content);
    }
}
