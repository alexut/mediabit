<?php
function get_file_version($file, $env) {
    // If the environment is not development and file doesn't exist, print an error message
    if ($env !== 'development' && !file_exists(get_stylesheet_directory() . $file)) {
        echo '<p>File does not exist: ' . get_stylesheet_directory() . $file . '</p>';
        return false;
    }
    return $env === 'development' ? time() : filemtime(get_stylesheet_directory() . $file);
}

function enqueue_file($handle, $file, $dependency, $media_type, $env) {
    $is_external = strpos($file, '//') !== FALSE;
    $ver = get_file_version($file, $env);
    
    if ($ver === false) {
        return; // Do not attempt to register or enqueue if the file does not exist
    }
    
    if ($is_external) {
        $uri = $file;
    } else {
        $uri = get_template_directory_uri() . $file;
    }

    // Check if dependencies are registered
    if (!empty($dependency)) {
        foreach ($dependency as $dep) {
            if (!wp_script_is($dep, 'registered')) {
                echo '<p>Dependency ' . $dep . ' for ' . $handle . ' is not registered.</p>';
                return; // Do not attempt to register or enqueue if a dependency is not registered
            }
        }
    }
    
    if ($media_type === 'scripts') {
        wp_enqueue_script($handle, $uri, $dependency, $ver, true);
        if (!wp_script_is($handle, 'enqueued')) {
            echo "<p>Failed to enqueue script: $handle</p>";
        }
    } else if ($media_type === 'styles') {
        wp_enqueue_style($handle, $uri, $dependency, $ver, 'all');
        if (!wp_style_is($handle, 'enqueued')) {
            echo "<p>Failed to enqueue style: $handle</p>";
        }
    }
}



function theme_enqueue() {
    
    $resources = [
        // Default scripts and styles
        'default' => [
            'scripts' => [
                'vendor-script' => ['/assets/js/vendor.js', '', true],
                'theme-script' => ['/assets/js/theme.js','', true],
            ],
            'styles' => [
                'theme-style' => ['/assets/css/theme.css', '', 'all'],
                'theme-font-icon' => ['//cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css', '', ''],
            ],
        ],
        // Template-specific scripts and styles
        'tpl-dashboard.php' => [
            'dequeue' => [
                'scripts' => ['theme-script'],
                'styles' => ['theme-style']
            ],
            'scripts' => [
                'dashboard-script' => ['/assets/js/dashboard.js','', true],
            ],
            'styles' => [
                'dashboard-style' => ['/assets/css/dashboard.css', '', 'all'],
            ],
        ],
        'tpl-dashboard2.php' => [
            'dequeue' => [
                'scripts' => ['theme-script'],
                'styles' => ['theme-style']
            ],
            'scripts' => [
                'dashboard-script' => ['/assets/js/dashboard.js','', true],
            ],
            'styles' => [
                'dashboard-style' => ['/assets/css/dashboard.css', '', 'all'],
            ],
        ],
        'tpl-brand.php' => [
            'dequeue' => [
                'scripts' => ['theme-script'],
                'styles' => ['theme-style']
            ],
            'scripts' => [
                'dashboard-script' => ['/assets/js/dashboard.js','', true],
            ],
            'styles' => [
                'dashboard-style' => ['/assets/css/dashboard.css', '', 'all'],
            ],
        ],
        'tpl-projects.php' => [
            'dequeue' => [
                'scripts' => ['theme-script'],
                'styles' => ['theme-style']
            ],
            'scripts' => [
                'dashboard-script' => ['/assets/js/dashboard.js','', true],
            ],
            'styles' => [
                'dashboard-style' => ['/assets/css/dashboard.css', '', 'all'],
            ],
        ],
        'tpl-facturi.php' => [
            'dequeue' => [
                'scripts' => ['theme-script'],
                'styles' => ['theme-style']
            ],
            'scripts' => [
                'dashboard-script' => ['/assets/js/dashboard.js','', true],
            ],
            'styles' => [
                'dashboard-style' => ['/assets/css/dashboard.css', '', 'all'],
            ],
        ],
    ];
    
    // Enqueue default scripts and styles
    foreach ($resources['default'] as $media_type => $files) {
        foreach ($files as $k => $v) {
            enqueue_file($k, $v[0], $v[1], $media_type, WP_ENV);
        }
    }
    
    // If a page template matches a key in resources, dequeue the specified scripts/styles and enqueue the new ones
    foreach ($resources as $template => $media) {
        if ($template !== 'default' && is_page_template($template)) {
            // Dequeue specified scripts and styles
            if (isset($media['dequeue'])) {
                foreach ($media['dequeue']['scripts'] as $script) {
                    wp_dequeue_script($script);
                }
                foreach ($media['dequeue']['styles'] as $style) {
                    wp_dequeue_style($style);
                }
            }
            // Enqueue new scripts and styles
            foreach ($media as $media_type => $files) {
                if ($media_type !== 'dequeue') {
                    foreach ($files as $k => $v) {
                        enqueue_file($k, $v[0], $v[1], $media_type, WP_ENV);
                    }
                }
            }
        }
    }

    wp_deregister_script('wp-mediaelement');
    wp_deregister_style('wp-mediaelement');
    wp_deregister_style('wp-block-library');
    wp_dequeue_style( 'wp-block-library' );

    // dequeue jquery ( already included )
    wp_deregister_script('jquery');
}


add_action('wp_enqueue_scripts', 'theme_enqueue');