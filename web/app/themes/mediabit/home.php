<?php
get_header();

$header = new \Mediabit\Templates\Sections\Header();
echo $header->render(); 

?>

<div class="container pt-7 pb-5">
    <h1 class="h2 mb-4"><?php single_post_title(); ?></h1>

    <?php if (have_posts()) : ?>
        <div class="row latest-posts">
            <?php
            while (have_posts()) : the_post();
                echo format_post(); // Use the same format_post function as in your shortcode
            endwhile;
            ?>
        </div>

        <div class="pagination mt-4">
            <?php
            // Pagination
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('« Prev', 'textdomain'),
                'next_text' => __('Next »', 'textdomain'),
            ));
            ?>
        </div>

    <?php else : ?>
        <div class="alert alert-info">No posts found.</div>
    <?php endif; ?>

</div>

<?php


$footer = new \Mediabit\Templates\Sections\Footer();
echo $footer->render();

$cookie = new \Mediabit\Templates\Sections\Cookie();
echo $cookie->renderGDPRScript();

get_footer();

// Function to format posts
function format_post() {
    $post_thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
    $output = '<div class="col-lg-6 col-xl-4"><div class="card mb-3 shadow-sm">';

    $output .= '<div class="card-body m-4">';
    $output .= '<div class="card-meta-date text-uppercase small pb-3">' . get_the_date() . '</div>';
    $output .= '<h4 class="card-title"><a class="text-primary" href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';
    $output .= '<p class="card-text">' . wp_trim_words(get_the_excerpt(), 20) . '</p>';
    $output .= '<p class="card-category pt-5"><a href="' . get_category_link(get_the_category()[0]->term_id) . '">' . get_the_category()[0]->name . '</a></p>';
    $output .= '<a href="' . get_permalink() . '" class="btn btn-link"><i class="bi bi-link-45deg"></i></a>';
    $output .= '</div></div></div>';  // card-body, card

    return $output;
}
