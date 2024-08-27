<?php
namespace Mediabit\Templates\Sections;

class Cookie {

    public function renderGDPRScript() {

    $analytics_html = <<<HTML
    <script type="text/plain" data-cookiecategory="analytics">
    // Code goes here
    </script>
    HTML;
    
    $advertising_html = <<<HTML
        <script type="text/plain" data-cookiecategory="advertising">
        // Code goes here
        </script>
    HTML;
        
        $gdpr_html = <<<HTML
        <script>
            // Inițializare
            window.addEventListener('load', function () {

                // obține pluginul
                var cc = initCookieConsent();

                // rulează pluginul cu configurația ta
                cc.run({
                    current_lang: 'ro',
                    autoclear_cookies: true,
                    page_scripts: true,

                    languages: {
                        'ro': {
                            consent_modal: {
                                title: 'Folosim cookie-uri!',
                                description: 'Folosim cookie-uri pe site-ul nostru pentru a îmbunătăți experiența dvs. de navigare, reținând preferințele dvs. și analizând traficul de pe site. Făcând clic pe "Acceptă toate", vă dați consimțământul pentru utilizarea tuturor cookie-urilor. Totuși, puteți gestiona <a data-bs-toggle="modal" href="#bs-cookie-modal">preferințele dvs. de cookie-uri</a> pentru a oferi un consimțământ controlat.',
                                primary_btn: {
                                    text: 'Acceptă toate',
                                    role: 'accept_all'
                                },
                                secondary_btn: {
                                    text: 'Respinge toate',
                                    role: 'accept_necessary'
                                },
                                settings_btn: {
                                    text: 'Preferințe'
                                },
                                consent_footer: {
                                    description: '<a class="small link-secondary text-decoration-none" href="/politica-de-confidentialitate">Politica de Confidențialitate</a> • <a class="small link-secondary text-decoration-none" href="/termeni-si-conditii">Termeni și Condiții</a>'
                                }
                            },

                            settings_modal: {
                                title: 'Preferințe pentru cookie-uri',
                                save_settings_btn: 'Salvează preferințele',
                                accept_all_btn: 'Acceptă toate',
                                reject_all_btn: 'Respinge toate',
                                close_btn_label: 'Închide',
                                cookie_table_headers: [
                                    { col1: 'Nume' },
                                    { col2: 'Domeniu' },
                                    { col3: 'Expirare' },
                                    { col4: 'Descriere' }
                                    ],
                                blocks: [
                                    {
                                        title: 'Utilizarea cookie-urilor',
                                        description: 'Folosim cookie-uri pentru a asigura funcționalitățile de bază ale site-ului și pentru a vă îmbunătăți experiența online. Puteți alege pentru fiecare categorie să acceptați/refuzați oricând doriți. Pentru mai multe detalii referitoare la cookie-uri și alte date sensibile, vă rugăm să citiți întreaga <a href="/politica-de-confidentialitate">Politică de Confidențialitate</a>.'
                                    }, 
                                    {
                                        title: 'Necesare',
                                        description: 'Aceste cookie-uri sunt esențiale pentru funcționarea corectă a site-ului nostru. Fără aceste cookie-uri, site-ul nu ar funcționa corespunzător.',
                                        toggle: {
                                            value: 'necessary', // NOTA: Valoarea trebuie păstrată în engleză pentru compatibilitate
                                            enabled: true,
                                            readonly: true          // categoriile de cookie-uri cu readonly=true sunt tratate ca "cookie-uri necesare"
                                        }
                                    }, 
                                    {
                                        title: 'Analitice',
                                        description: 'Aceste cookie-uri permit site-ului să rețină alegerile pe care le-ați făcut în trecut și să colecteze date pentru analiza traficului.',
                                        toggle: {
                                            value: 'analytics', // NOTA: Valoarea trebuie păstrată în engleză pentru compatibilitate
                                            enabled: false,
                                            readonly: false
                                        },
                                        cookie_table: [           // lista tuturor cookie-urilor așteptate
                                            {
                                                col1: '^_ga',         // corespunde tuturor cookie-urilor care încep cu "_ga"
                                                col2: 'google.com',
                                                col3: '2 ani',
                                                col4: 'Acest cookie este utilizat pentru a distinge utilizatorii unici, prin atribuirea unui identificator generat aleator.',
                                                is_regex: true
                                            }, 
                                            {
                                                col1: '_gid',
                                                col2: 'google.com',
                                                col3: '1 zi',
                                                col4: 'Acest cookie este utilizat pentru a distinge utilizatorii unici pe un site prin stocarea și actualizarea unui identificator unic.'
                                            }
                                        ]
                                    }, 
                                    {
                                        title: 'Mai multe informații',
                                        description: 'Pentru orice întrebări legate de politica noastră privind cookie-urile și opțiunile dvs., vă rugăm să <a href="/contact">ne contactați</a>.',
                                    }
                                ]
                            }
                        }
                    }
                });
            });
        </script>
        {$analytics_html}
        HTML;
        return $gdpr_html;
    }



    
}
