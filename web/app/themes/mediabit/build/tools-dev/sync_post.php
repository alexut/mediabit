<?php
class PostTypeSync {
    
    private static $_doing_import = false;

    private static function get_syncdir() {
        return get_template_directory() . '/sources/posts/';
    }
    

    public static function export($post_type) {
        $posts = get_posts([
            'post_type' => $post_type,
            'numberposts' => -1
        ]);
    
        if (!empty($posts)) {
            $posts_data = [];
            foreach ($posts as $post) {
                $posts_data[] = self::export_post($post, $post_type);
            }
    
            $syncdir = self::get_syncdir();
            if (!file_exists($syncdir)) {
                mkdir($syncdir, 0777, true);
            }
            $filename = $post_type . '.json';
            $target = $syncdir . '/' . $filename;
    
            file_put_contents($target, json_encode($posts_data, JSON_PRETTY_PRINT));
    
            return true;
        } else {
            return false;
        }
    }
    

    private static function prepare_post_data($post, $post_type) {
        $taxonomies = get_object_taxonomies($post_type);
        $terms_data = [];
        foreach ($taxonomies as $taxonomy) {
            $terms_data[$taxonomy] = wp_get_post_terms($post->ID, $taxonomy, ['fields' => 'ids']);
        }

        return [
            'ID' => $post->ID,
            'post_name' => $post->post_name,
            'post_content' => $post->post_content,
            'post_title' => $post->post_title,
            'post_date' => $post->post_date,
            'acf_fields' => get_fields($post->ID),
            'taxonomies' => $terms_data,
            'featured_image' => get_post_thumbnail_id($post->ID)
        ];
    }

    public static function import($post_type) {
        self::$_doing_import = true; // Set the _doing_import variable to true
        $syncdir = self::get_syncdir();
        $filename = $post_type . '.json';
        $file_path = $syncdir . '/' . $filename;
    
        if (!file_exists($file_path)) {
            return;
        }

    
        $contents = file_get_contents($file_path);


        $posts_data = json_decode($contents, true);
   
        foreach ($posts_data as $post_data) {
            self::import_post($post_data, $post_type);
        }
        
        self::$_doing_import = false;
    }
    

    private static function import_post($post_data, $post_type) {

        $name = $post_data['post_name'];
    
        $posts = get_posts([
            'name' => $name,
            'post_type' => $post_type,
            'posts_per_page' => 1,
            'ignore_sticky_posts' => 1
        ]);
    
        if (!empty($posts)) {
            $post = $posts[0];
            kses_remove_filters();
            wp_update_post(['ID' => $post->ID, 'post_content' => $post_data['post_content']]);
            wp_update_post(['ID' => $post->ID, 'post_date' => $post_data['post_date']]);
            kses_init_filters();
    
            // Update ACF fields
            if (!empty($post_data['acf_fields'])) {
                foreach ($post_data['acf_fields'] as $field_key => $field_value) {
                    update_field($field_key, $field_value, $post->ID);
                }
            }
    
            // Update taxonomies
            if (!empty($post_data['taxonomies'])) {
                foreach ($post_data['taxonomies'] as $taxonomy => $terms) {
                    wp_set_object_terms($post->ID, $terms, $taxonomy);
                }
            }
    
            // Update featured image
            if (!empty($post_data['featured_image'])) {
                set_post_thumbnail($post->ID, $post_data['featured_image']);
            }
        } else {
            $post_id = wp_insert_post([
                'post_name' => $post_data['post_name'],
                'post_content' => $post_data['post_content'],
                'post_title' => $post_data['post_title'],
                'post_date' => $post_data['post_date'],
                'post_type' => $post_type,
                'post_status' => 'publish'
            ]);
    
            // Update ACF fields
            if (!empty($post_data['acf_fields'])) {
                foreach ($post_data['acf_fields'] as $field_key => $field_value) {
                    update_field($field_key, $field_value, $post_id);
                }
            }
    
            // Update taxonomies
            if (!empty($post_data['taxonomies'])) {
                foreach ($post_data['taxonomies'] as $taxonomy => $terms) {
                    wp_set_object_terms($post_id, $terms, $taxonomy);
                }
            }
    
            // Update featured image
            if (!empty($post_data['featured_image'])) {
                set_post_thumbnail($post_id, $post_data['featured_image']);
            }
        }
    }
    

    public static function on_post_update($post_id, $post, $update) {
        if (self::$_doing_import) { // Check if we are currently importing
            return; // If so, do not export
        }
        // Check if the post type matches
        if (in_array($post->post_type, ['post', 'product', 'package'])) { // Add your custom post types here
            // Call the export_post method
            self::export($post->post_type);
        }
    }
    private static function export_post($post, $post_type) {
        $taxonomies = get_object_taxonomies($post_type);
        $terms_data = [];
        foreach ($taxonomies as $taxonomy) {
            $terms_data[$taxonomy] = wp_get_post_terms($post->ID, $taxonomy, ['fields' => 'ids']);
        }
    
        return [
            'post_name' => $post->post_name,
            'post_content' => $post->post_content,
            'post_title' => $post->post_title,
            'post_date' => $post->post_date,
            'acf_fields' => get_fields($post->ID),
            'taxonomies' => $terms_data,
            'featured_image' => get_post_thumbnail_id($post->ID)
        ];
    }
    
}

add_action('init', function() {
    // PostTypeSync::import('post'); // Import posts
    // PostTypeSync::import('product'); // Import products
    // PostTypeSync::import('offer'); // Export posts
});
add_action('save_post', [PostTypeSync::class, 'on_post_update'], 10, 3);

