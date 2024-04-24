<?php
get_header();


$header = new \Mediabit\Templates\Sections\Header();
echo $header->render(); 

if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        the_content();
    }
}

$footer = new \Mediabit\Templates\Sections\Footer();
echo $footer->render();

get_footer();
?>