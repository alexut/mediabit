<?php

namespace Mediabit\Templates\Partials;

class Logo {
    
  public function render()
    {
    // get site title
    $siteTitle = get_bloginfo('name');
    
    // get site logo
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );

   if ( has_custom_logo() ) { 
        $logoHtml = '<a href="' . get_home_url() . '" class="navbar-brand"><img class="logo-brand" src="' . esc_url( $logo[0] ) . '" alt="' . $siteTitle . '"></a>';
    } else {
        $logoHtml = '<a href="' . get_home_url() . '" class="navbar-brand">' . $siteTitle . '</a>';
    }
    
    return $logoHtml;
    
    }
}