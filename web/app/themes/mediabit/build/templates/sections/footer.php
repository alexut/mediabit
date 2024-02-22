<?php
namespace Mediabit\Templates\Sections;

use Mediabit\Templates\Partials;

class Footer {
    public function render()
    {
        $copyrightHtml = $this->renderCopyright();
        $socialLinksHtml = $this->renderSocialLinks();
        // get media with id 80


        $footerHtml = <<<HTML
            <footer>
                <div class="container text-center text-lg-left">
                    <div class="d-lg-flex justify-content-between py-4 small">
                        {$copyrightHtml}
                        <div class="footer-links">
                            {$socialLinksHtml}
                        </div>
                    </div>
                </div>
            </footer>
        HTML;
        return $footerHtml;
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
            <p>© Copyright {$year}. 
                <a href="{$home_url}">{$site_name}</a> - All Rights Reserved.</p>
            </p>
        HTML;
    
        return $copyrightHtml;
    }
}