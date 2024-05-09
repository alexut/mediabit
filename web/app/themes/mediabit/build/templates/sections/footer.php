<?php
namespace Mediabit\Templates\Sections;

use Mediabit\Templates\Partials;

class Footer {
    public function render()
    {
        $copyrightHtml = $this->renderCopyright();
        $socialLinksHtml = $this->renderSocialLinks();
        $logoHtml = $this->renderLogo();
        $anpcHtml = $this->renderAnpc();

        $solutiiHtml =  wp_nav_menu(array(
            'theme_location' => 'solutions-menu',  // or your specific theme location
            'container'      => false,
            'menu_class' => 'list-unstyled',
            'echo' => false
        ));
        $serviciiHtml =  wp_nav_menu(array(
            'theme_location' => 'services-menu',  // or your specific theme location
            'container'      => false,
            'menu_class' => 'list-unstyled',
            'echo' => false
        ));
        // get media with id 80

        // cf7 shortcode

        $form_abonare = do_shortcode('[contact-form-7 id="ad144c0"]');


        $footerHtml = <<<HTML
            <footer class="bg-light pt-5">
            <div class="container">
                <div class="row">
                <div class="col-xl-4 col-lg-5">
                    <h4 class="w-75 mt-3">Soluții web simple pentru probleme complexe.</h4>
                    <p class="pb-4">Dezvoltăm soluții web, optimizate pentru motoarele de căutare, care îți vor aduce mai mulți clienți 
                    și îți vor crește vânzările.</p>
                    
                    <h5>Abonează-te la newsletter-ul nostru lunar și primește o broșură gratuită despre creșterea afacerii în online.</h5>
                    <div class="nl-subscription pb-4">
                    {$form_abonare}
                    </div>
           
                </div>
                <!-- Secțiunea cu Link-uri -->
                <div class="col-lg-6 offset-lg-1 col-xl-6 offset-xl-2">
                    <div class="row">
                        <div class="col-xl-4 col-sm-5 col-6">
                        <h4 class="h5 mt-3">Servicii</h4>
                            {$serviciiHtml}
                        </div>
                        <div class="col-xl-5 col-sm-6 col-12">
                            <h4 class="h5 mt-3">Soluții B2B</h4>
                            {$solutiiHtml}
                        </div>
                        <div class="col-xl-4 col-sm-4 col-6">
                            <h4 class="h5 mt-3">Despre Noi</h4>
                            <ul class="list-unstyled mt-3">
                                <li><a href="/despre-noi/">Despre Noi</a></li>
                                <li><a href="/blog/">Blog</a></li>
                                <li><a href="/contact/">Contact</a></li>
                                <!-- <li><a href="/testimoniale">Testimoniale</a></li> -->
                            </ul>
                        </div>
                        <div class="col-xl-4 col-sm-4 col-6">
                            <h4 class="h5 mt-3">Suport</h4>
                            <ul class="list-unstyled mt-3">
                                <!-- <li><a href="/blog/">Ghid Online</a></li> -->
                                <li><a href="/login/">Suport Clienți</a></li>
                                <li><a href="/blog/">Întrebari Frecvente</a></li>
                                <li><a href="/blog/">Deschide Tichet</a></li>
                            </ul>
                        </div>
                        <div class="col-xl-4 col-sm-4 col-6">
                            <h4 class="h5 mt-3">Politici</h4>
                            <ul class="list-unstyled mt-3">
                                <!-- <li><a href="/politica-de-confidentialitate/">Confidențialitate</a></li> -->
                                <li><a href="/termeni-si-conditii/">Termeni și Condiții</a></li>
                                <li><a href="/politica-de-cookies/">Politica de Cookies</a></li>
                                <li><a href="/politica-de-cookies/">Setări GDPR</a></li>
                            </ul>
                        </div>
                    </div>
                    {$socialLinksHtml}
                </div>
                <div class="col-lg-12 pt-3">
                {$copyrightHtml}
                </div>
                <div class="col-lg-12">
                    {$anpcHtml}
                </div>
                </div>
            </div>
            </footer>
        HTML;
        return $footerHtml;
    }

    private function renderAnpc() {
        $anpcHtml = <<<HTML
            <div class="mb-anpc row my-3 g-2 g-lg-4">
                <a class="col-xl-2 col-md-3 col-6 pb-1" href="https://anpc.ro/ce-este-sal/" target="_blank">
                    <img class="w-100" src="/app/themes/mediabit/assets/images/anpc1.png" alt="ANPC">
                </a>
                <a class="col-xl-2  col-md-3  col-6 pb-1" href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home2.show&lng=RO" target="_blank">
                    <img class="w-100" src="/app/themes/mediabit/assets/images/anpc2.png" alt="ANPC">
                </a>
               
                <a class="col-xl-2  col-md-3  col-6 pb-1" href="https://www.sslshopper.com/ssl-checker.html#hostname=mediabit.ro" target="_blank">
                    <img class="w-100" src="/app/themes/mediabit/assets/images/ssl.png" alt="SSL">
                </a>
         
                <a class="col-xl-2  col-md-3  col-6 pb-1" href="" target="_blank">
                    <img class="w-100" src="/app/themes/mediabit/assets/images/trust.png" alt="Trusted">
                </a>
            </div>

        HTML;
        return $anpcHtml;
    }

    private function renderLogo() {
        $logo = new Partials\Logo();
        $logoHtml = $logo->render();
        return $logoHtml;
    }

    private function renderSocialLinks()
    {
        // [social_links] shortcode
        $socialLinks = do_shortcode('[social_links]');

        $socialHtml = <<<HTML
            {$socialLinks}
        HTML;
        
        return $socialHtml;
    }

    private function renderCopyright()
    {
        $site_name = get_bloginfo('name');
        $year = date('Y');
        $home_url = get_permalink( '/'  );
        $copyrightHtml = <<<HTML
            <p>© {$year}. 
                <a href="{$home_url}">Third Wave Labs SRL</a> - Toate drepturile rezervate.
            </p>
        HTML;
    
        return $copyrightHtml;
    }
}