<?php

// TODO - make sure this works only in development/staging environment. maybe we can create an automation that ignores the file when pushing to production environment

require_once('wp/wp-load.php'); // Path to wp-load.php

if (isset($_GET['user_id'])) {
    // Auto-login functionality
    function auto_login() {
        if (isset($_GET['auto_login']) && $_GET['auto_login'] == '1' && isset($_GET['user_id']) && isset($_GET['token'])) {
            $user_id = intval($_GET['user_id']);
            $token = sanitize_text_field($_GET['token']);
            $stored_token = get_transient('login_token_' . $user_id);

            if ($token === $stored_token && !empty($stored_token)) {
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                delete_transient('login_token_' . $user_id); // Delete the token after successful login

                // Redirect to the dashboard or desired location
                wp_redirect(admin_url());
                exit;
            }
        }
    }
    auto_login();
}

function search_and_display_users() {
    if (isset($_GET['user'])) {
        $search_term = sanitize_text_field($_GET['user']);

        $user_query = new WP_User_Query(array(
            'search' => '*' . esc_attr($search_term) . '*',
            'search_columns' => array('user_login', 'user_nicename', 'user_email'),
        ));

        $users = $user_query->get_results();

        if (!empty($users)) {
            echo '<ul>';
            foreach ($users as $user) {
                echo '<li>' . esc_html($user->user_login) . ' - <a href="' . esc_url(get_login_link($user->ID)) . '">Login as this user</a></li>';
            }
            echo '</ul>';
        } else {
            echo 'No users found.';
        }
    }
    // Display the link for user with id 1
    echo '<a href="' . esc_url(get_login_link(1)) . '">Admin Login</a>';
}

function get_login_link($user_id) {
    $token = generate_login_token($user_id);
    return $_SERVER['PHP_SELF'] . '?auto_login=1&user_id=' . $user_id . '&token=' . $token;
}

function generate_login_token($user_id) {
    $token = wp_generate_password(20, false);
    set_transient('login_token_' . $user_id, $token, 300); // Token valid for 5 minutes
    return $token;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>WP User Search Dashboard</title>
</head>
<body>
    <h1>Search for Users</h1>
    <?php search_and_display_users(); ?>
</body>
</html>