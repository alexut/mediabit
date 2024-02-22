<?php
// Define the custom permalink structure
add_action( 'init',  function() {
    add_rewrite_rule( 'handler/([a-z0-9-]+)[/]?$', 'index.php?handler=$matches[1]', 'top' );
} );

// Whitelist the query param: handler
add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'handler';
    return $query_vars;
} );

// Add a handler to send it off to a template file: custom-handler.php

add_action( 'template_include', function( $template ) {
    if ( get_query_var( 'handler' ) == false || get_query_var( 'handler' ) == '' ) {
        return $template;
    }
    return get_template_directory() . '/custom-handler.php';
} );

// Define the custom permalink structure for login

add_action( 'init',  function() {
    add_rewrite_rule( 'login[/]?$', 'index.php?action=login', 'top' );
} );

// Whitelist the query param: action
add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'action';
    return $query_vars;
} );

// Add a handler to send it off to a template file: custom-login.php
add_action( 'template_include', function( $template ) {
    if ( get_query_var( 'action' ) == false || get_query_var( 'action' ) == '' ) {
        return $template;
    }
    return get_template_directory() . '/custom-login.php';
} );
