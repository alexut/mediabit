<?php
// Exit if accessed directly
defined('ABSPATH') || exit;
?>

<aside id="secondary" class="sidebar">

    <!-- Conditional Button -->
    <div class="order-button mb-4 p-5 shadow-sm bg-primary rounded text-center">
        <h3 class="h4 text-white mb-3">Vrei un site care să facă diferența?</h3>
        <?php
        // Determine the current category
        $current_category = get_queried_object();

        // Check the category slug or ID and set the appropriate URL
        if (isset($current_category->slug) && ($current_category->slug === 'dezvoltare-website' || $current_category->slug === 'design')) {
            // For categories related to web development or design
            $button_url = '/realizare-website/#site-demo-gratuit';
            $button_text = 'Cere un design demo gratuit';
        } else {
               // For categories related to web development or design
               $button_url = '/realizare-website/#site-demo-gratuit';
               $button_text = 'Cere o cotație';
        }
        ?>
        <a href="<?php echo esc_url($button_url); ?>" class="btn btn-light d-block">
            <?php echo esc_html($button_text); ?>
        </a>
    </div>
    <!-- Recent Posts -->
    <div class="recent-posts mb-4 p-5 shadow-sm bg-white">
        <h3 class="sidebar-title h4 text-primary">Cele mai recente</h3>
        <ul class="list-unstyled">
            <?php
            $recent_posts = wp_get_recent_posts(array(
                'numberposts' => 5, // Number of recent posts thumbnails to display
                'post_status' => 'publish' // Show only the published posts
            ));
            foreach ($recent_posts as $post) : ?>
                <li class="py-1">
                    <a href="<?php echo get_permalink($post['ID']) ?>">
                        <?php echo esc_html($post['post_title']); ?>
                    </a>
                </li>
            <?php endforeach;
            wp_reset_query(); ?>
        </ul>
    </div>

    <!-- Categories -->
    <div class="categories mb-4 p-5 shadow-sm bg-white">
        <h3 class="h4 sidebar-title text-primary">Categorii</h3>
        <ul class="list-unstyled">
            <?php
            $categories = get_categories();
            foreach ($categories as $category) : ?>
                <li class="py-1">
                    <a href="<?php echo get_category_link($category->term_id); ?>">
                        <?php echo esc_html($category->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>


</aside>
