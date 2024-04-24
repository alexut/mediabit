<?php 
namespace Mediabit\Templates\Sections;


class Sidebar {
    public function render( )
    {
   
        $dir = get_template_directory_uri();
        $sidebar_html = <<<HTML

            <div class="sidebar">
                <div>
                    <a class="button button-lg active mb-1" href="#">
                        <div class="button-inner">
                            <img src="$dir/assets/images/dashboard/mediabit.svg" />
                        </div>
                    </a>
                </div>
                <div></div>
                <div>
                    <a class="button" href="/dashboard">
                        <div class="button-inner">
                            <div>
                                <svg class="icon-set icon-medium" xmlns="http://www.w3.org/2000/svg" role="img">
                                    <title>Dashboard</title>
                                    <use xlink:href="/app/themes/mediabit/assets/icons/icons.svg#edit"></use>
                                </svg>
                            </div>
                        </div>
                        <div class="button-text">Dashboard</div>
                    </a>
                    <a class="button" href="/project">
                        <div class="button-inner">
                            <svg class="icon-set icon-medium" xmlns=http://www.w3.org/2000/svg role="img" >
                                <title>Proiecte</title>
                                <use xlink:href="$dir/assets/icons/icons.svg#document"></use>
                            </svg>
                        </div>
                        <div class="button-text">Proiecte</div>
                    </a>
                    <a class="button" href="/brand">
                        <div class="button-inner">
                            <svg class="icon-set icon-medium" xmlns=http://www.w3.org/2000/svg role="img" >
                                    <title>cloudy</title>
                                    <use xlink:href="$dir/assets/icons/icons.svg#user-edit"></use>
                            </svg>
                        </div>
                        <div class="button-text">Brand</div>
                    </a>
                    <a class="button mb-0" href="/facturi">
                        <div class="button-inner">
                            <svg class="icon-set icon-medium" xmlns=http://www.w3.org/2000/svg role="img" >
                                <title>cloudy</title>
                                <use xlink:href="$dir/assets/icons/icons.svg#wallet-money"></use>
                            </svg>
                        </div>
                        <div class="button-text">Documente</div>
                    </a>
                </div>
                <div class="separator my-4"></div>
                <div>
                    <a class="button" href="#">
                        <i class="bi bi-gear text-secondary"></i>
                    </a>
                    <a class="button" href="#">
                        <i class="bi bi-box-arrow-right text-primary"></i>
                    </a>
                </div>
            </div>
        HTML;

        return $sidebar_html;
    }


}
?>