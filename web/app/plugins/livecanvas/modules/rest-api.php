<?php 
// EXIT IF ACCESSED DIRECTLY.
defined( 'ABSPATH' ) || exit;

//LC REST API for the EDITOR: GET HTML ELS FROM DB: POSTS OF ANY CPT
add_action("rest_api_init", function  () {
    register_rest_route("livecanvas/v1", "/posts-by-tax/", [
        "methods" => "GET",
        "callback" => "lc_get_all_posts_by_custom_tax",
        'permission_callback' => function () { 
            return current_user_can('edit_pages');
        },
        "args" => [
            "post_type" => [
                "required" => true,
                "validate_callback" => function ($param, $request, $key) {
                    return post_type_exists($param);
                },
            ],
            "tax_name" => [
                "required" => true,
                "validate_callback" => function ($param, $request, $key) {
                    return taxonomy_exists($param);
                },
            ],
        ],
    ]);
});

function lc_get_all_posts_by_custom_tax($request) { 

    // Step 1: Retrieve all posts of the specified post type
    $all_posts_args = [
        'post_type' => $request["post_type"],
        'posts_per_page' => -1, // Retrieve all posts
        'fields' => 'ids', // Only get post IDs to enhance performance
    ];

    $all_posts_ids = get_posts($all_posts_args);
    $tax_terms = get_terms(['taxonomy' => $request["tax_name"], 'hide_empty' => false]);

    $posts_by_tax = [];
    foreach ($tax_terms as $tax_term) {
        $posts_by_tax[$tax_term->term_id] = [
            'name' => $tax_term->name,
            'posts' => [],
        ];
    }

    // Flag to track if any posts are uncategorized
    $has_uncategorized_posts = false;

    // Step 2: Assign posts to their term or mark for "Uncategorized"
    foreach ($all_posts_ids as $post_id) {
        $terms = wp_get_post_terms($post_id, $request["tax_name"], ['fields' => 'ids']);
        $term_id = $terms ? $terms[0] : 'none';

        // If no term is found, check if "Uncategorized" needs to be added
        if ($term_id == 'none') {
            if (!$has_uncategorized_posts) {
                // Add "Uncategorized" dynamically
                $posts_by_tax['none'] = [
                    'name' => 'Uncategorized',
                    'posts' => [],
                ];
                $has_uncategorized_posts = true;
            }
        }

        $post_info = [
            'title' => get_the_title($post_id),
            'id' => $post_id,
            'content' => get_post_field('post_content', $post_id, 'raw'),
            
        ];

        // Assign the post to the corresponding term or "Uncategorized"
        $posts_by_tax[$term_id]['posts'][] = $post_info;
    }

    // Step 3: Prepare the response, excluding empty "Uncategorized" if necessary
    $resp = ['categories' => []];
    foreach ($posts_by_tax as $tax_term_id => $info) {
        if ($tax_term_id == 'none' && !$has_uncategorized_posts) {
            continue; // Skip empty "Uncategorized"
        }
        $resp['categories'][] = [
            'category_id' => $tax_term_id !== 'none' ? $tax_term_id : 'none',
            'category_name' => $info['name'],
            'pages' => $info['posts'],
        ];
    }

    return new WP_REST_Response($resp, 200);
}

//ONE MORE REST API for the EDITOR: GET HTML ELS FROM CHILD THEME FOLDERS
add_action("rest_api_init", function () {
    register_rest_route("livecanvas/v1", "/html-files-from-theme/", [
        "methods" => "GET",
        "callback" => "lc_get_html_files_from_theme",
        'permission_callback' => function () {
            return current_user_can('edit_pages');
        },
        "args" => [
            "folder" => [
                "required" => true,
            ],
        ],
    ]);
});

function lc_get_html_files_from_theme($request) {
    // First, check if a child theme is active
    if (get_stylesheet_directory() === get_template_directory()) {
        return new WP_REST_Response([
            'error' => 'child_theme_not_active', 
        ], 428);
    }

    $base_folder = sanitize_text_field($request['folder']);
    $base_directory_path = get_stylesheet_directory() . '/' . $base_folder;

    // Check if the specified folder exists within the child theme directory
    if (!is_dir($base_directory_path)) {
        return new WP_REST_Response([
            'error' => 'folder_not_found',
        ], 406);
    }

    // Fetch and return HTML files...
    $categories = [];
    // Include root directory files as a default/main category
    $root_html_files = lc_get_html_files_in_directory($base_directory_path);
    if (!empty($root_html_files)) {
        $categories[] = [
            'category_id' => crc32($base_directory_path), // Generate a pseudo ID for the root category
            'category_name' => 'Uncategorized', //ucwords(str_replace('-', ' ', basename($base_folder))), // Beautify the base folder name
            'pages' => $root_html_files,
        ];
    }

    // Check for subdirectories and include them as separate categories
    
    $base_dir = new DirectoryIterator($base_directory_path);
    
    foreach ($base_dir as $dir) {
        if ($dir->isDot() || !$dir->isDir()) continue; // Skip non-directories and dot directories

        $category_name = ucwords(str_replace('-', ' ', $dir->getFilename())); // Beautify the directory name
        $category_path = $dir->getPathname();
        $html_files = lc_get_html_files_in_directory($category_path);

        // Add category and its pages if it contains any HTML files
        if (!empty($html_files)) {
            $categories[] = [
                'category_id' => crc32($category_path), // Generate a pseudo ID
                'category_name' => $category_name,
                'pages' => $html_files,
            ];
        }
    }
    
    return new WP_REST_Response(['categories' => $categories], 200);
}

// Helper function to get HTML files in a directory
function lc_get_html_files_in_directory($directory_path) {
    $html_files = [];
    $files = new DirectoryIterator($directory_path);

    foreach ($files as $file) {
        if ($file->isDot() || !$file->isFile()) continue;
        if ($file->getExtension() === 'html') {
            $file_path = $file->getPathname();
            $content = file_get_contents($file_path);
            $html_files[] = [
                'title' => $file->getBasename('.html'),
                'id' => crc32($file_path), // Generate a pseudo ID
                'content' => $content,
            ];
        }
    }

    return $html_files;
}


