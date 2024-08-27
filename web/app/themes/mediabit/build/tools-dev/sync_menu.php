<?php
class MenuSync {
    private static function get_syncdir() {
        return get_template_directory() . '/sources/menus/';
    }

    public static function import() {
        $syncdir = self::get_syncdir();
        $dir = new DirectoryIterator($syncdir);

        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                self::import_menu($fileinfo->getPathname());
            }
        }
    }

    private static function import_menu($json_path) {
        $json_data = file_get_contents($json_path);
        $data = json_decode($json_data, true);
    
        if (!$data) {
            echo "Error decoding JSON.";
            return;
        }
    
        $menu_name = $data['menu_name'];
        $menu_location = $data['menu_location'];
        $items = $data['items'];
    
        // Delete existing menu with the same name
        $existing_menu = wp_get_nav_menu_object($menu_name);
        if ($existing_menu) {
            wp_delete_nav_menu($existing_menu->term_id);
        }
    
        // Create new menu
        $menu_id = wp_create_nav_menu($menu_name);
        if (is_wp_error($menu_id)) {
            echo 'Failed to create menu: ' . $menu_id->get_error_message();
            return;
        }
    
        // Add menu items
        foreach ($items as $item) {
            self::add_menu_item($menu_id, 0, $item);
        }
    
        // Set menu location
        $locations = get_theme_mod('nav_menu_locations');
        $locations[$menu_location] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);

        echo "Menu '{$menu_name}' created and set to location '{$menu_location}'.";
    }
    
    private static function add_menu_item($menu_id, $parent_id, $item) {
        // Ensure 'classes' is a string for 'menu-item-classes'
        $classes = isset($item['classes']) ? (is_array($item['classes']) ? implode(' ', $item['classes']) : $item['classes']) : '';
    
        $item_data = array(
            'menu-item-title' => $item['title'],
            'menu-item-url' => $item['url'],
            'menu-item-classes' => $classes, // Ensure this is a string
            'menu-item-target' => isset($item['target']) ? $item['target'] : '', // Check if target exists
            'menu-item-attr-title' => isset($item['attr_title']) ? $item['attr_title'] : '',
            'menu-item-description' => isset($item['description']) ? $item['description'] : '',
            'menu-item-parent-id' => $parent_id,
            'menu-item-type' => 'custom',
            'menu-item-status' => 'publish'
        );
    
        $item_id = wp_update_nav_menu_item($menu_id, 0, $item_data);
        if (is_wp_error($item_id)) {
            echo 'Failed to add/update menu item: ' . $item_id->get_error_message();
            return;
        }
    
        // Handle submenus recursively
        if (isset($item['submenu']) && is_array($item['submenu'])) {
            foreach ($item['submenu'] as $subitem) {
                self::add_menu_item($menu_id, $item_id, $subitem);
            }
        }
    }
    
    
}

if (isset($_GET['importmenu']) && $_GET['importmenu'] == 1) {
    MenuSync::import();
    echo 'Imported';
    die();
}
