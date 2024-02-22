<?php
namespace Mediabit\Handlers;

class Login {
    public function login() {
        $login = new \Mediabit\Templates\Sections\Login();

        if ( isset( $_POST['login_nonce'] ) && wp_verify_nonce( $_POST['login_nonce'], 'login_form' ) ) {
            if (isset($_POST['log']) && isset($_POST['pwd'])) {
                $creds = array(
                    'user_login'    => $_POST['log'],
                    'user_password' => $_POST['pwd'],
                    'remember'      => isset($_POST['rememberme']) && $_POST['rememberme'] == 'forever'
                );

                $user = wp_signon($creds, false);

                if (is_wp_error($user)) {
                    // login failed, return the login form with an error message
                    $login_form = $login -> renderLoginForm('error');
                    echo $login_form;
                } else {
                    // login successful, return a JSON response with the redirect URL
                    $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : home_url();
                    echo $login -> renderLoginForm('success');
                }
            }
        }
    }
}
