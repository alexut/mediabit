<?php 
namespace Mediabit\Templates\Sections;

class Login {

    function render() {
        $login_form = $this->renderLoginForm();

        $login_card = <<<HTML
            <div class="card login-form mt-5">
                <div class="card-header text-center">
                    <h4>Login</h4>
                </div>
                <div class="card-body">
                    {$login_form}
                </div>
                
            </div>
        HTML;
        return $login_card;
    }

    public function renderLoginForm($status = false ) {
        $nounce = wp_nonce_field('login_form', 'login_nonce', true, false);
        $redirect_to = home_url();
        $url = esc_attr(home_url('/handler/login'));
        if ($status == 'error') {
            $status = <<<HTML
                <div class="alert alert-danger" role="alert">
                    Invalid username or password.
                </div>
                <button type="submit" class="btn btn-primary">Try Again</button>
            HTML;
        } else if ($status == 'success') {
            header('HX-Refresh: true');
            $status = <<<HTML
                <button type="submit" class="btn btn-success">Bravo</button>
            HTML;
        } else {
            $status = <<<HTML
                <button type="submit" class="btn btn-primary">Login</button>
            HTML;
        }
        $login_form = <<<HTML
            <div class="login-form">
                <form id="login-form" class="mt-4" hx-post="{$url}" hx-target="#login-form" hx-swap="outerHTML">
                    <div class="form-group mb-3">
                        <label for="user_login">Username</label>
                        <input type="text" name="log" id="user_login" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="user_pass">Password</label>
                        <input type="password" name="pwd" id="user_pass" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="checkbox" name="rememberme" id="rememberme" value="forever">
                        <label for="rememberme">Remember Me</label>
                    </div>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>">
                    {$status}
                    {$nounce}
                </form>
            </div>
        HTML;
    
        return $login_form;
    }
    

}