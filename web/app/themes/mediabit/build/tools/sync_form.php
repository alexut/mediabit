<?php

class FormSync {
    
    private static $_doing_import = false;

    private static function get_syncdir() {
        return get_template_directory() . '/sources/forms/';
    }
    
    public static function export($post_type) {
        $posts = get_posts([
            'post_type' => $post_type,
            'numberposts' => -1
        ]);
    
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $form_data = self::prepare_post_data($post);
                
                $syncdir = self::get_syncdir();
                if (!file_exists($syncdir)) {
                    mkdir($syncdir, 0777, true);
                }
                
                // Filename is now simply the ID of the post
                $filename = $post->ID . '.php';
                $target = $syncdir . $filename;
                
                // Prepare the data to be saved in PHP format
                $data_to_save = "<?php\n" . '$form_data = ' . var_export($form_data, true) . ";\n";
                file_put_contents($target, $data_to_save);
            }
    
            return true;
        } else {
            return false;
        }
    }

    public static function import($post_type) {
        self::$_doing_import = true;
        $syncdir = self::get_syncdir();
        
        if (!is_dir($syncdir) || !is_readable($syncdir)) {
            self::$_doing_import = false;
            return;
        }
        
        $files = glob($syncdir . '*.php');
        foreach ($files as $file) {
            unset($form_data);
            include($file);
          
            if (isset($form_data) && is_array($form_data)) {
                self::import_form($form_data, $post_type);
            }
        }
        
        self::$_doing_import = false;
    }
    

    private static function import_form($form_data, $post_type) {
        // No need to check hash; rely on ID directly
        $post_id = wp_update_post([
            'ID' => $form_data['ID'],
            'post_title' => $form_data['post_title'],
            'post_content' => $form_data['post_content'],
            'post_name' => $form_data['post_name'],
            'post_date' => $form_data['post_date'],
            'post_type' => $post_type,
            'post_status' => 'publish'
        ]);
    
        // Update meta values if post was successfully updated or created
        if ($post_id > 0) {
            update_post_meta($post_id, '_form', $form_data['_form']);
            update_post_meta($post_id, '_mail', $form_data['_mail']);
            update_post_meta($post_id, '_mail_2', $form_data['_mail_2']);
            update_post_meta($post_id, '_messages', $form_data['_messages']);
        }
    
        return $post_id;
    }
    

    private static function prepare_post_data($post) {

          // Fetch meta values
    $form_meta = get_post_meta($post->ID, '_form', true);
    $mail_meta = get_post_meta($post->ID, '_mail', true);
    $mail_2_meta = get_post_meta($post->ID, '_mail_2', true);
    $messages_meta = get_post_meta($post->ID, '_messages', true);

    return [
        'ID' => $post->ID,
        'post_name' => $post->post_name,
        'post_content' => '',
        'post_title' => $post->post_title,
        'post_date' => $post->post_date,
        '_form' => $form_meta,
        '_mail' => $mail_meta,
        '_mail_2' => $mail_2_meta,
        '_messages' => $messages_meta,
    ];
    }

    public static function on_post_update($post_id, $post, $update) {
        if (self::$_doing_import) {
            return;
        }
        if ($post->post_type === 'wpcf7_contact_form') {
            self::export($post->post_type);
        }
    }
}

if (isset($_GET['exportform']) && $_GET['exportform'] == 1) {
    FormSync::export('wpcf7_contact_form');
    echo 'exported';
    die();
}
if (isset($_GET['importform']) && $_GET['importform'] == 1) {
    FormSync::import('wpcf7_contact_form');
    echo 'imported';
    die();
}

// add_action('save_post', [FormSync::class, 'on_post_update'], 10, 3);