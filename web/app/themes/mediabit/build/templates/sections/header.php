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

    private function renderMenu() {
        // $menu = wp_nav_menu(array(
        //     'theme_location' => 'main-menu',
        //     'container' => false,
        //     'menu_class' => 'navbar-nav m-auto animate-event',
        //     'fallback_cb' => '__return_false',
        //     'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        //     'depth' => 2,
        //     'walker' => new \Boootstrap_Walker_Nav(),
        //     'echo' => false
        // ));
        // return $menu;

        // custom menu

        $link_fitness = get_permalink( get_page_by_path( 'site-uri-de-fitness-sport-arte-martiale-studiouri-de-dans' ) );
        $link_website = get_permalink( get_page_by_path( 'realizare-website' ) );
  
        $link_seo = get_permalink( get_page_by_path( 'optimizare-seo' ) );
        $link_marketing = get_permalink( get_page_by_path( 'marketing-digital' ) );
        $link_mentenanta = get_permalink( get_page_by_path( 'mentenanta-website' ) );


        $menuHtml = <<<HTML
        <ul id="menu-main-menu" class="navbar-nav m-auto animate-event">
            <li id="menu-item-1" class="nav-item">
                <a href="/" class="nav-link">Acasă</a>
            </li>
            <li class="nav-item dropdown lc-block"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="10,20">
                Servicii Complete
            </a>
            <ul class="dropdown-menu pb-0 shadow" aria-labelledby="navbarDropdownMenuLink">

                <li class="rounded-3 p-lg-3">
                    <a class="dropdown-item d-flex align-items-center gap-4" href="{$link_website}">
                        <i class="bi bi-code-slash"></i>
                        <p class="mb-0 small">
                            <span class="fw-semibold text-gray-900">Realizare Website</span>
                            <span class="d-none d-lg-block mb-0 mt-1 text-gray-400 fw-normal">Creăm site-uri web personalizate.</span>
                        </p>
                    </a>
                </li>
                <li class="rounded-3 p-lg-3">
                    <a class="dropdown-item d-flex align-items-center gap-4" href="#">
                        <i class="bi bi-bar-chart-line"></i>
                        <p class="mb-0 small">
                            <span class="fw-semibold text-gray-900 text-wrap">Optimzare SEO</span>
                            <span class="d-none d-lg-block mb-0 mt-1 text-gray-400 fw-normal"> Creștem vizibilitatea site-ului tău.</span>
                        </p>
                    </a>
                </li>
                <li class="rounded-3 p-lg-3">
                    <a class="dropdown-item d-flex align-items-center gap-4" href="#">
                        <i class="bi bi-people"></i>
                        <p class="mb-0 small">
                            <span class="fw-semibold text-gray-900">Marketing Digital</span>
                            <span class="d-none d-lg-block mb-0 mt-1 text-gray-400 fw-normal">Promovăm afacerea ta online.</span>
                        </p>
                    </a>
                </li>
                <li class="rounded-3 p-lg-3">
                    <a class="dropdown-item d-flex align-items-center gap-4" href="#">
                        <i class="bi bi-gear"></i>
                        <p class="mb-0 small">
                            <span class="fw-semibold text-gray-900 text-wrap">Mentenanță Website</span>
                            <span class="d-none d-lg-block mb-0 mt-1 text-gray-400 fw-normal">Găzduire, securitate, actualizări.</span>
                        </p>
                    </a>
                </li>
            
            </ul>
        </li>

        <li class="nav-item dropdown lc-block"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="10,20">
                Soluții B2B
            </a>
            <ul class="dropdown-menu pb-0 shadow" aria-labelledby="navbarDropdownMenuLink">

                <li class="rounded-3 p-lg-3">
                    <a class="dropdown-item d-flex align-items-center gap-4" href="{$link_fitness}">
                        <i class="bi bi-code-slash"></i>
                        <p class="mb-0 small">
                            <span class="fw-semibold text-gray-900">Site-uri Fitness</span>
                            <span class="d-none d-lg-block mb-0 mt-1 text-gray-400 fw-normal">Site-uri pentru sălile de fitness, arte marțiale și studiouri de dans.</span>
                        </p>
                    </a>
                </li>

                <li class="rounded-3 p-lg-3">
                    <a class="dropdown-item d-flex align-items-center gap-4" href="#">
                        <i class="bi bi-bar-chart-line"></i>
                        <p class="mb-0 small">
                            <span class="fw-semibold text-gray-900 text-wrap">Site-uri Medicină</span>
                            <span class="d-none d-lg-block mb-0 mt-1 text-gray-400 fw-normal">Site-uri pentru cabinete medicale, clinici și spitale.</span>
                        </p>
                    </a>
                </li>


                <li class="rounded-3 p-lg-3">
                    <a class="dropdown-item d-flex align-items-center gap-4" href="#">
                        <i class="bi bi-bar-chart-line"></i>
                        <p class="mb-0 small">
                            <span class="fw-semibold text-gray-900 text-wrap">Configurare si rezervări online cu AI</span>
                            <span class="d-none d-lg-block mb-0 mt-1 text-gray-400 fw-normal">Soluții pentru rezervări online cu inteligență artificială.</span>
                        </p>
                    </a>
                </li>

                <!-- <li class="rounded-3 p-lg-3">
                    <a class="dropdown-item d-flex align-items-center gap-4" href="#">
                        <i class="bi bi-people"></i>
                        <p class="mb-0 small">
                            <span class="fw-semibold text-gray-900">Site-uri Saloane</span>
                            <span class="d-none d-lg-block mb-0 mt-1 text-gray-400 fw-normal">Site-uri pentru saloane de înfrumusețare, coafor și cosmetică.</span>
                        </p>
                    </a>
                </li>
                <li class="rounded-3 p-lg-3">
                    <a class="dropdown-item d-flex align-items-center gap-4" href="#">
                        <i class="bi bi-gear"></i>
                        <p class="mb-0 small">
                            <span class="fw-semibold text-gray-900 text-wrap">Site-uri Industrie</span>
                            <span class="d-none d-lg-block mb-0 mt-1 text-gray-400 fw-normal">Site-uri pentru companii din industria prelucrătoare și producție.</span>
                        </p>
                    </a> -->
                </li>


                <li class="rounded-3 p-lg-3">
                    <a class="dropdown-item d-flex align-items-center gap-4" href="#">
                        <i class="bi bi-gear"></i>
                        <p class="mb-0 small">
                            <span class="fw-semibold text-gray-900 text-wrap">Soluții Ecommerce</span>
                            <span class="d-none d-lg-block mb-0 mt-1 text-gray-400 fw-normal">Platforme de vânzare online pentru magazine și producători.</span>
                        </p>
                    </a>
                </li>
                <!-- <div class="d-flex justify-content-between bg-slate-100 small rounded-bottom">
                    <div class="py-3 text-center col-6 border-end-lg">
                        <a class="d-flex justify-content-center w-100 text-slate-800 text-decoration-none link-primary align-items-center gap-2" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16">
                                <path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"></path>
                            </svg>
                            Prezentare Video</a>
                    </div>
                    <div class="py-3 text-center col-6">
                        <a class="d-flex justify-content-center w-100 text-slate-800 text-decoration-none link-primary align-items-center gap-2" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" style="">
                                <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"></path>
                            </svg>
                            Contact</a>
                    </div>
                </div> -->
            </ul>
        </li>

     
            <li id="menu-item-4" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-4 nav-item">
                <a href="/blog" class="nav-link">Blog</a>
            </li>
            <li id="menu-item-5" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-5 nav-item">
                <a href="/contact" class="nav-link">Contact</a>
            </li>
        </ul>
        HTML;
        return $menuHtml;
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
