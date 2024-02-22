<?php
get_header();
// the content loop

if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        the_content();
    }
}


get_footer();
?>