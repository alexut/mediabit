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

    private function renderMenuButton() {
        $buttonHtml = <<<HTML
        <button class="hamburger-icon navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i></i>
            <span>Meniu</span>
        </button>
        HTML;
        return $buttonHtml;
    }

    private function renderAvatar() {
        $avatar = new Partials\Avatar();
        $avatarHtml = $avatar->render();
        return $avatarHtml;
    }

    private function renderMenu() {
        $menu = wp_nav_menu(array(
            'theme_location' => 'main-menu',
            'container' => false,
            'menu_class' => 'navbar-nav m-auto',
            'fallback_cb' => '__return_false',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'depth' => 2,
            'walker' => new \Boootstrap_Walker_Nav(),
            'echo' => false
        ));
        return $menu;
    }

    private function renderSearch() {
        $search = new Partials\Search();
        $searchHtml = $search->render();
        return $searchHtml;
    }

    private function renderMenuArea() {
        $logoHtml = $this->renderLogo();
        $buttonHtml = $this->renderMenuButton();
        $searchHtml = $this->renderSearch();
        $navHtml = $this->renderMenu();
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
            <a class="btn btn-sm btn-link fw-bold" href="/login">Log In</a>
            <a class="btn btn-sm btn-outline-primary animate-event" href="#">Vreau un website</a>
            HTML;
        }
    
        $headerHtml = <<<HTML
        <header class="header header0">
            <div class="container">  
                <div class="row">
                    <div class="col">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            {$logoHtml}
                            {$buttonHtml}
                            <div class="navbar-collapse offcanvas-collapse animation-container-lg collapse" id="navbarSupportedContent" style="">
                                {$navHtml}
                                {$authHtml}
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </header>
        HTML;
    
        return $headerHtml;
    }
    
}
?>
