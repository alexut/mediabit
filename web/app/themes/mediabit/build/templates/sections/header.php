<?php 
namespace Mediabit\Templates\Sections;

use Mediabit\Templates\Partials;

class Header {
    public function render()
    {
        $headerHtml = $this->renderMenuArea();
        return $headerHtml;
    }

    private function renderLogo() {
        $logo = new Partials\Logo();
        $logoHtml = $logo->render();
        return $logoHtml;
    }

    private function renderCall() {
        // https://wa.me/15551234567?text=I%20want%20to%20build%20a%20website
        $logohtml = <<<HTML
        <div class="mb-call ms-lg-4 ms-xl-6">
        <a href="https://wa.me/40771594504?text=Vreau%20un%20website" target="_blank">
        <svg class="icon-set icon-primary icon-small" xmlns="http://www.w3.org/2000/svg" role="img"><title>whatsapp</title><use xlink:href="/app/themes/mediabit/assets/icons/icons.svg#whatsapp"></use></svg>
            <span class="d-none d-sm-inline-block">(+40) 771 594 504</span></a>
        </div>
        HTML;
        return $logohtml;
    }

    private function renderMenuButton() {
        $buttonHtml = <<<HTML
        <a class="hamburger-icon navbar-toggler collapsed" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i></i>
            <span>Meniu</span>
        </a>
        HTML;
        return $buttonHtml;
    }

    private function renderAvatar() {
        $avatar = new Partials\Avatar();
        $avatarHtml = $avatar->render();
        return $avatarHtml;
    }



    private function renderSearch() {
        $search = new Partials\Search();
        $searchHtml = $search->render();
        return $searchHtml;
    }

    private function renderMenuArea() {
        $logoHtml = $this->renderLogo();
        $callHtml = $this->renderCall();
        $buttonHtml = $this->renderMenuButton();
        $searchHtml = $this->renderSearch();
        $navHtml =  wp_nav_menu(array(
            'theme_location' => 'main-menu',  // or your specific theme location
            'walker'         => new \Custom_Bootstrap_Walker_Nav(),
            'container'      => false,
            'menu_class'     => 'navbar-nav m-auto',  // update class as needed
            'echo' => false
        ));
        $avatarHtml = $this->renderAvatar();
    
        if (is_user_logged_in()) {
            $currentUser = wp_get_current_user();
            $username = $currentUser->user_login;
            $authHtml = <<<HTML
            {$avatarHtml}
            <span class="username">{$username}</span>
            HTML;
        } else {
            $authHtml = <<<HTML
                <a class="btn btn-sm btn-outline-primary animate-event" href="#">Solicită o ofertă</a>
                <a class="btn btn-sm btn-link fw-bold animate-event" href="/login"><svg class="icon-small icon-set icon-primary" xmlns="http://www.w3.org/2000/svg" role="img"><title>user-octagon</title><use xlink:href="/app/themes/mediabit/assets/icons/icons.svg#user-octagon"></use></svg></a>
            HTML;
        }
    
        $headerHtml = <<<HTML
        <!-- // fixed navbar -->
        
        <header class="header header0">
            <nav class="navbar navbar-expand-lg navbar-light">
                {$logoHtml}
                {$callHtml}
                {$buttonHtml}
                <div class="navbar-collapse offcanvas-collapse animation-container-lg collapse text-center" id="navbarSupportedContent" style="">
                    {$navHtml}
                    {$authHtml}
                </div>
            </nav>
        </header>

        HTML;
    
        return $headerHtml;
    }
    
}
?>
