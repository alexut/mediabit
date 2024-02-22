<?php

namespace Mediabit\Templates\Partials;

class Sidebar {
    public function render_category_link($category, $taxonomy, $is_child = false) {
       
        $category_name = $category->name;
        $category_class = $is_child ? 'product-cat-child' : 'product-cat';
        // if child than $category_slug is category->slug else $category_slug is 'parent'
       if ($is_child) {
            $category_slug = $category->slug;
            $category_link ='<a href="#'. $category_slug .'">'. $category_name .'</a>';
        } else {
            $category_slug = 'children';
            $category_link = '<span>'. $category_name .'</span>';
        }

        return <<<HTML
        <span class="d-block {$category_class}">
            <a class="checkmark-link" data-toggle="{$category_slug}">
                <i class="bi bi-check-circle-fill"></i>
            </a>
            {$category_link}
        </span>
    HTML;
        }

    public function render_sidebar($post_type = 'post', $taxonomy = 'category') {
        $categories = get_terms([
            'taxonomy' => $taxonomy,
            // order by count
            'orderby' => 'count',
            'order' => 'DESC',
            'hide_empty' => false,
            'parent' => 0, // Get only top-level categories
        ]);

        $post_type_name = get_post_type_object($post_type)->labels->name;

        $category_links = '';
        foreach ($categories as $category) {
            $category_links .= $this->render_category_link($category, $taxonomy, false);

            $subcategories = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'parent' => $category->term_id, // Get subcategories of the current category
            ]);

            foreach ($subcategories as $subcategory) {
                $category_links .= $this->render_category_link($subcategory, $taxonomy, true);
            }
        }

        $sidebar_html = <<<HTML
            <div class="shadow-sm bg-white rounded p-4 mb-4 position-sticky" style="top: 20px">
                <h4>{$post_type_name}</h4>
                <div class="product-cat-wrap active" id="side-categs">
                    {$category_links}
                </div>
            </div>
        HTML;

        return $sidebar_html;
    }
}
