<?php 
namespace Mediabit\Templates\Sections;

use Mediabit\Templates\Partials;

class Heading {
    public function render($title, $date)
    {
        $headerHtml = <<<HTML
        
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <a class="bell d-lg-none me-2" style="width: 40px; height: 40px; border-radius: 10px;" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas"
                        aria-controls="sidebarOffcanvas" href="#">
                        <svg
                            class="icon-set icon-small icon-white" xmlns="http://www.w3.org/2000/svg"
                            role="img">
                            <title>edit</title>
                            <use xlink:href="/app/themes/mediabit/assets/icons/icons.svg#arrow-right-1">
                            </use>
                        </svg>
                    </a>
                    <div>
                        <h1>$title</h1>
                        <p class="mb-0">$date</p>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a class="bell active" href="#">
                        <svg class="icon-set icon-medium icon-primary icon-white" xmlns="http://www.w3.org/2000/svg"
                            role="img">
                            <title>edit</title>
                            <use xlink:href="/app/themes/mediabit/assets/icons/icons.svg#notification">
                            </use>
                        </svg>
                    </a>
                    <div>
                        <a class="bell ms-1 d-xl-none" data-bs-toggle="offcanvas" data-bs-target="#profileOffcanvas"
                            aria-controls="profileOffcanvas" href="#">
                            <i class="bi bi-person h2 mb-0 text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        
        HTML;
        return $headerHtml;
    }
    
}
?>