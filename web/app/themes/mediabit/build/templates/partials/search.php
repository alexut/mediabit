<?php

namespace Mediabit\Templates\Partials;

class Search {
    
  public function render( $type = 'none' ) {
    if ( $type = 'mobile' ) {
        $class = 'mb-0';
    } else {
        $class = 'mb-0 d-none d-sm-block';
    }
    // 
    // wordpress search
    $searchHtml = <<<HTML
        <form role="search" method="get" class="{$class}">
            <div class="input-group input-group-search">
                <button class="btn border-0 p-0" type="button" id="button-addon1"><i class="fa-solid fa-magnifying-glass"></i></button>
                <input type="search" class="form-control p-0 border-0" placeholder="Search" value="" name="s" title="Search for:">
            </div>
        </form>
    HTML;
    return $searchHtml;
    }
}