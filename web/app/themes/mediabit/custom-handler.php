<?php 
/**
 * Custom Handler
 */

use Mediabit\Handlers\Login;

$queryvar = get_query_var( 'handler' );

if (isset(($queryvar))) {
    switch (($queryvar)) {
        case 'login':
            // Handle login action here
            $task = new Login();
            $task->login();
            break;
        case 'register':
            // Handle register action here
            break;
        default:
            // Handle unknown action here
            echo 'Unknown';
            break;
    }
}