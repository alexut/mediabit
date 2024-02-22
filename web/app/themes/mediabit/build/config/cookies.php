<?php

// TODO: Create a script that automatically scans the cookies and sends the json data of the cookies to the script to generate or at least add the scripts in an enqueue script array that will also add theme in the cookie array.
// Block / manage scripts
// Set type="text/plain" and data-cookiecategory="<category>" to any script tag you want to manage. Use inline-script HTML widget in Footer 4 position after the init script.

// Examples:
// <!-- Google Analytics -->
// <script type="text/plain" data-cookiecategory="analytics">
//   (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
//   (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
//   m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
//   })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

//   ga('create', 'UA-XXXXX-Y', 'auto');
//   ga('send', 'pageview');
// </script>

// <!-- Advertising -->
// <script type="text/plain" data-cookiecategory="advertising" src="./assets/js/my_custom_script.js" defer></script>

class CookieConsent
{
    private $cookies;

    public function __construct($cookies)
    {
        $this->cookies = $cookies;
    }

    public function generateScript()
    {
        $cookieCategories = $this->generateCookieCategories();
        $cookieScript = "
            <script>
                window.addEventListener('load', function () {
                    var cc = initCookieConsent();
                    cc.run({
                        current_lang: 'en',
                        autoclear_cookies: true,
                        page_scripts: true,
                        languages: {
                            'en': {
                                consent_modal: {
                                    title: 'We use cookies!',
                                    description: 'We use cookies on our website to give you the most relevant experience by remembering your preferences and repeat visits. By clicking “Accept all”, you consent to the use of all the cookies. However, you may visit \"Cookie Settings\" to provide a controlled consent. <a data-bs-toggle=\"modal\" href=\"#bs-cookie-modal\">Cookie Settings</a>',
                                    primary_btn: {
                                      text: 'Accept all',
                                      role: 'accept_all'
                                    },
                                    secondary_btn: {
                                      text: 'Reject all',
                                      role: 'accept_necessary'
                                    }
                                  },
                                settings_modal: {
                                    title: 'Cookie settings',
                                    save_settings_btn: 'Save settings',
                                    accept_all_btn: 'Accept all',
                                    reject_all_btn: 'Reject all',
                                    close_btn_label: 'Close',
                                    cookie_table_headers: [
                                      { col1: 'Name' },
                                      { col2: 'Domain' },
                                      { col3: 'Expiration' },
                                      { col4: 'Description' }
                                    ],
                                    blocks: [
                                        // ... other blocks ...
                                        $cookieCategories
                                    ]
                                }
                            }
                        }
                    });
                });
            </script>
        ";

        return $cookieScript;
    }

    private function generateCookieCategories()
    {
        $cookieCategories = "";
        foreach ($this->cookies as $category => $categoryData) {
            $cookieCategory = "
                {
                    title: '{$categoryData['title']}',
                    description: '{$categoryData['description']}',
                    toggle: {
                        value: '{$category}',
                        enabled: {$categoryData['enabled']},
                        readonly: {$categoryData['readonly']}
                    },
                    cookie_table: [";
            
            foreach ($categoryData['cookies'] as $cookie) {
                $cookieCategory .= "
                    {
                        col1: '{$cookie['name']}',
                        col2: '{$cookie['domain']}',
                        col3: '{$cookie['expiration']}',
                        col4: '{$cookie['description']}',
                        is_regex: {$cookie['is_regex']}
                    },";
            }

            $cookieCategory .= "
                    ]
                },";

            $cookieCategories .= $cookieCategory;
        }

        return $cookieCategories;
    }
}



function mediabit_cookie_consent_script() {
    $site_url = parse_url(get_site_url());
    $domain = $site_url['host'];
    
    $cookies = [
        'necessary' => [
            'title' => 'Necessary',
            'description' => 'These cookies are essential for the proper functioning of the website.',
            'enabled' => 'true',
            'readonly' => 'true',
            'cookies' => [
                [
                    'name' => 'wordpress_sec_*',
                    'domain' => $domain,
                    'expiration' => 'Session',
                    'description' => 'WordPress security cookie.',
                    'is_regex' => 'true'
                ],
                [
                    'name' => 'wp-settings-time-1',
                    'domain' => $domain,
                    'expiration' => '1 year',
                    'description' => 'WordPress cookie for user settings.',
                    'is_regex' => 'false'
                ],
                [
                    'name' => 'wordpress_logged_in_*',
                    'domain' => $domain,
                    'expiration' => 'Session',
                    'description' => 'WordPress cookie for logged-in users.',
                    'is_regex' => 'true'
                ],
                [
                    'name' => 'wp-settings-1',
                    'domain' => $domain,
                    'expiration' => '1 year',
                    'description' => 'WordPress cookie for user settings.',
                    'is_regex' => 'false'
                ],
                [
                    'name' => 'wordpress_test_cookie',
                    'domain' => $domain,
                    'expiration' => 'Session',
                    'description' => 'WordPress test cookie to check if cookies are enabled.',
                    'is_regex' => 'false'
                ],
            ]
        ],
    ];

    $cookieConsent = new CookieConsent($cookies);
    echo $cookieConsent->generateScript();
}

// Hook the function after body
add_action('wp_footer', 'mediabit_cookie_consent_script');

