<?php namespace Mediabit\Shortcodes;

use Mediabit\Shortcodes\Wrapper;
use WP_Query;

class LatestPosts {
    public static function init() {
        Wrapper::register('latest_posts', [self::class, 'render_shortcode']);
    }

    public static function render_shortcode($atts) {
        $atts = shortcode_atts([
            'class' => '',
            'category' => '',
            'count' => 3  // Default to showing 3 posts
        ], $atts, 'latest_posts');

        if (empty($atts['category'])) {
            return '<div class="alert alert-warning">No category specified.</div>';  // Error handling for no category
        }

        $args = [
            'category_name' => $atts['category'],
            'posts_per_page' => $atts['count']
        ];

        $query = new WP_Query($args);
        $posts_found = $query->found_posts;

        $result = '<div class="row latest-posts ' . esc_attr($atts['class']) . '">';

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $result .= self::format_post();
            }
        }

        // Check if the number of posts in the category is less than the requested count
        if ($posts_found < $atts['count']) {
            $additional_posts_needed = $atts['count'] - $posts_found;
            $global_args = [
                'posts_per_page' => $additional_posts_needed,
                'post__not_in' => wp_list_pluck($query->posts, 'ID') // Exclude posts already fetched
            ];

            $global_query = new WP_Query($global_args);

            if ($global_query->have_posts()) {
                while ($global_query->have_posts()) {
                    $global_query->the_post();
                    $result .= self::format_post();
                }
            }
        }

        if (!$posts_found && !$global_query->have_posts()) {
            $result .= '<div class="alert alert-info">No posts found.</div>';
        }

        $result .= '</div>';  // latest-posts

        wp_reset_postdata();  // Reset global post object to avoid conflicts
        return $result;
    }

    private static function format_post() {
        $post_thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
        $output = '<div class="col-lg-6 col-xl-4"><div class="card mb-3 shadow-sm">';


        $output .= '<div class="card-body m-4">';
                $output .= '<div class="card-meta-date text-uppercase small pb-3">' . get_the_date() . '</div>';
        $output .= '<h4 class="card-title"><a class="text-primary" href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';
        $output .= '<p class="card-text">' . wp_trim_words(get_the_excerpt(),20) . '</p>';
        $output .= '<p class="card-category pt-5"><a href="' . get_category_link(get_the_category()[0]->term_id) . '">' . get_the_category()[0]->name . '</a></p>';
        $output .= '<a href="' . get_permalink() . '" class="btn btn-link"><i class="bi bi-link-45deg"></i></a>';
        $output .= '</div></div></div>';  // card-body, card

        return $output;
    }

}

LatestPosts::init();
?>
