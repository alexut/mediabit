<?php
class PageSync {
    private static function get_syncdir() {
        return get_template_directory() . '/sources/pages/';
    }

    public static function export() {
        $pages = get_pages();

        if (!empty($pages)) {
            foreach ($pages as $page) {
                self::export_page($page);
            }

            return true;
        } else {
            return false;
        }
    }

    private static function export_page($page) {
        $syncdir = self::get_syncdir();
        $filename = strtolower(str_replace(' ', '-', $page->post_name)) . '.html';
    
        $target = $syncdir . '/' . $filename;
        
        if (!empty($page->post_content)) {
            echo $filename . ' exported<br>';
            $file = fopen($target, 'w');
            fwrite($file, $page->post_content);
            fclose($file);
        }
    }

    public static function import() {
        $syncdir = self::get_syncdir();

        $dir = new DirectoryIterator($syncdir);
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                self::import_page($fileinfo);
            }
        }
    }
 
    private static function import_page($fileinfo) {
        $syncdir = self::get_syncdir();
        $name = str_replace('-', ' ', ucfirst(substr($fileinfo->getBasename('.' . $fileinfo->getExtension()), 0)));

        $name = preg_replace('/-{2,}/', '-', $name); // If there are two or more dashes, keep only one

        $pages = get_posts([
            'name' => $name,
            'post_type' => 'page',
            'posts_per_page' => 1,
            'ignore_sticky_posts' => 1
        ]);

    
        if (!empty($pages)) {
            // if post date is older than file date update post
            $filedate = filemtime($fileinfo->getPathname());
            $postdate = strtotime($pages[0]->post_date);


            if ($filedate > $postdate) {

                $file = fopen($fileinfo->getPathname(), 'r');
                $contents = fread($file, filesize($fileinfo->getPathname()));
                fclose($file);
        
                $page = $pages[0];
                kses_remove_filters();
                remove_filter('the_content', 'wpautop');
                wp_update_post(['ID' => $page->ID, 'post_content' => $contents]);
                wp_update_post(['ID' => $page->ID, 'post_date' => date('Y-m-d H:i:s', $filedate)]);
                add_filter('the_content', 'wpautop');
                kses_init_filters();
            
            }

        } else {
            // create post
            $file = fopen($fileinfo->getPathname(), 'r');
            $contents = fread($file, filesize($fileinfo->getPathname()));
            fclose($file);
            $post = [
                'post_title' => $name,
                'post_name' => $name,
                'post_content' => $contents,
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_date' => date('Y-m-d H:i:s', filemtime($fileinfo->getPathname()))
            ];
            kses_remove_filters();
            remove_filter('the_content', 'wpautop');
            wp_insert_post($post);
            add_filter('the_content', 'wpautop');
            kses_init_filters();
        }
    }

    public static function on_page_update($post_id, $post, $update) {
   
        // Check if the post type is 'page'
        if ($post->post_type === 'page') {
         
            // Call the export_page method
            self::export_page($post);
        }
    }

}

if (isset($_GET['exportpage']) && $_GET['exportpage'] == 1) {
    PageSync::export();
    // log and break
    echo 'exported';
    die();
}

if (isset($_GET['importpage']) && $_GET['importpage'] == 1) {
    PageSync::import();
    // log and break
    echo 'imported';
    die();
}

// add_action('save_post', 'PageSync::on_page_update', 10, 3);