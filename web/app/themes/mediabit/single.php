<?php
get_header();

$header = new \Mediabit\Templates\Sections\Header();
echo $header->render(); 

?>

<div id="content" class="site-content container pt-7 pb-5  bg-light">
    <div id="primary" class="content-area">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                 <div class="entry-meta text-body-secondary text-center">
                 <header class="entry-header py-5">
                                        <small class="fw-bold text-primary">
                                            <span><?php echo get_the_date( 'j F Y' ); ?></span>
                                            <!-- category -->
                                            <?php if (get_the_category()) : ?>
                                                <span class="ms-2">
                                                    <?php the_category(', '); ?>
                                                </span>
                                            <?php endif; ?>
                                        </small>
                    <h1 class="h2 entry-title text-center"><?php the_title(); ?></h1>
                </header>
            </div>
            </div>
        <div class="row justify-content-center gx-lg-6">
            <div class="col-lg-7">

                <main id="main" class="site-main">
                    
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                
                            
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail">
                                            <?php the_post_thumbnail('full', ['class' => 'img-fluid']); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                 
                                    
                                 

                                <div class="entry-content">
                                    <?php the_content(); ?>
                                </div>
                                
                                <footer class="entry-footer">
                                    <?php if (has_tag()) : ?>
                                        <div class="tags mb-4">
                                            <?php the_tags('<span class="badge bg-secondary">', '</span> <span class="badge bg-secondary">', '</span>'); ?>
                                        </div>
                                    <?php endif; ?>
                                    

                                    <?php
                                    // If comments are open or we have at least one comment, load up the comment template.
                                    if (comments_open() || get_comments_number()) :
                                        comments_template();
                                    endif;
                                    ?>
                                </footer>

                            </article>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <p><?php _e('Sorry, no posts matched your criteria.', 'textdomain'); ?></p>
                    <?php endif; ?>
                    
                </main>

                

            </div>
            <div class="col-lg-4">
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</div>
<!-- posts section -->
<section>
	<div class="container py-5 mt-lg-6 py-lg-6 border-top border-bottom">
		<div class="row justify-content-center">
			<div class="col-11 col-md-8 col-lg-6 text-center">
				<h3 class="h3">Articole similare</h3>
				<p class="pb-4 pb-lg-5">Articolele similare cu cel pe care tocmai l-ai citit</p>
			</div>		
		</div>
		<div class="row">
			<?php echo do_shortcode('[latest_posts class="custom-class" category="news" count="3"]'); ?>
		</div>
	</div>
</section>

<?php

$footer = new \Mediabit\Templates\Sections\Footer();
echo $footer->render();

$cookie = new \Mediabit\Templates\Sections\Cookie();
echo $cookie->renderGDPRScript();

get_footer();
?>
