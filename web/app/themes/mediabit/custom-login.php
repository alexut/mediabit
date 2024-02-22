<?php
/**
 * Custom Login Page
 */

if ( is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}
get_header(); // Load the header template

use Mediabit\Templates\Sections\Login;

$login = new Login();
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php echo $login->render(); ?>
        </div>
    </div>
</div>

<?php
get_footer(); // Load the footer template
