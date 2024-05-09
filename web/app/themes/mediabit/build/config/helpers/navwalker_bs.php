<?php
class Custom_Bootstrap_Walker_Nav extends Walker_Nav_Menu {

        
    // Start Level - Wrapping the submenu in ul with dropdown-menu class
    function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"dropdown-menu pb-0 shadow\" aria-labelledby=\"navbarDropdownMenuLink\">\n";
    }

// Start Element - Outputting each menu item
function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    $indent = ($depth) ? str_repeat("\t", $depth) : '';
    $li_attributes = '';
    $class_names = $value = '';

    $classes = empty($item->classes) ? array() : (array) $item->classes;
    $classes[] = 'nav-item'; // Ensure nav-item is added to every item.

    // Add dropdown class to parent menu items
    if ($args->walker->has_children) {
        $classes[] = 'dropdown';
    }

    // Format class names and apply filters
    $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
    $class_names = ' class="' . esc_attr($class_names) . '"';

    $id = apply_filters('nav_menu_item_id', 'menu-item-'.$item->ID, $item, $args);
    $id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';

    $output .= $indent . '<li' . $id . $class_names . $li_attributes . '>';

    $attributes = !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
    $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
    $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

    // Add 'nav-link' and possibly 'dropdown-toggle' classes to links
    $item_output = $args->before;
    $link_class = 'nav-link';
    if ($depth === 0 && $args->walker->has_children) {
        $link_class .= ' dropdown-toggle';
        $attributes .= ' id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"';
    }
    if ($depth > 0) {
        $link_class = 'dropdown-item d-flex align-items-center gap-3';
    }
    $item_output .= '<a class="' . $link_class . '"' . $attributes . '>';

    if ($depth > 0 ) {
        if (!empty($item->attr_title) ) {
            $item_output .=  '<i class="' . $item->attr_title . '"></i>';
        }
        $item_output .= '<p class="mb-0 small">';
        $item_output .= '<span class="fw-semibold text-gray-900 text-wrap">' . apply_filters('the_title', $item->title, $item->ID) . '</span>';
        if (!empty($item->description) ) {
            $item_output .= '<span class="d-none d-lg-block m-0 text-gray-400 font-base">' . $item->description . '</span>';
        }
        $item_output .= '</p>';
    } else {
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
    }

    
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }
        
    
        function end_lvl(&$output, $depth = 0, $args = array()) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }
    
        function end_el(&$output, $item, $depth = 0, $args = array()) {
            $output .= "</li>\n";
        }
    }
    
