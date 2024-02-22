<?php
/*
* delete--Sample--/deleteTemplate Name: Sample Template 
*/
// Get header

if ( !is_user_logged_in() ) {
    wp_redirect( home_url('/login') );
    exit;
}

get_header();
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Sample Template</h1>
        </div>
    </div>
</div>

<?php
// Get footer
get_footer();
?>
