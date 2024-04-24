<?php
namespace Mediabit\Shortcodes;

use Mediabit\Shortcodes\Wrapper;
use Mediabit\DropboxFactory;

class DropboxAuth {
    public static function init() {
        Wrapper::register('dropbox_auth', [__CLASS__, 'render']);
    }

    public static function render($atts, $content = null) {
        $dropbox = DropboxFactory::create();
        
        $authHelper = $dropbox->getAuthHelper();

        $callbackUrl = home_url('/handler/dropbox-auth');
        $authUrl = $authHelper->getAuthUrl($callbackUrl);
        
        // move DBAPI_state from $_SESSION to options
        $state = $_SESSION['DBAPI_state'];
        update_option('dropbox_state', $state);
        
        return '<br><a href="' . esc_url($authUrl) . '">Connect with Dropbox</a>';
    }
}

// Initialize the DropboxAuth shortcode.
DropboxAuth::init();