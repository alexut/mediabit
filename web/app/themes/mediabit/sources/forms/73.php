<?php
$form_data = array (
  'ID' => 73,
  'post_name' => 'testare',
  'post_content' => '',
  'post_title' => 'Formular Creare Website',
  'post_date' => '2024-03-21 12:00:00',
  '_hash' => '8e912e8266bbfb705a5f768c6ff95fc6096a819e',
  '_form' => '<!-- Pasul 1: Informații Generale -->
  <div class="step active" id="step-1">
    <div class="container">
      <h2  class="h4 mb-4">Informații Generale</h2>
      <div class="row">
        <div class="col-lg-6">
        <div class="mb-3">
          <label class="form-label">Numele și prenumele dvs.</label>
            [text* your-name class:form-control placeholder "Numele și prenumele dvs."]
          </div>
        </div>
        <div class="col-lg-6">
          <div class="mb-3">
            <label class="form-label">Adresa dvs. de email</label>
            [email* your-email class:form-control placeholder "Adresa dvs. de email"]
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="mb-3">
            <label class="form-label">CUI Firmă</label>
            [text cui-firma class:form-control placeholder "CUI Firmă"]
          </div>
        </div>
        <div class="col-lg-6">
          <div class="mb-3">
            <label class="form-label">Domeniu de activitate</label>
            [select* domeniu-activitate class:form-select first_as_label "Alege-ti o optiune" "IT & Tech" "Medicina & Farma" "Sport & Fitness" "Turism" "Educație" "ONG" "Altele"]
          </div>
        </div>
      </div>
     
      <button type="button" class="btn btn-primary go-to-step" data-target-step="#step-2">Următorul Pas</button>
    </div>
  </div>
  
  <!-- Pasul 2: Funcționalități și Design -->
  <div class="step" id="step-2">
    <div class="container">
      <h2  class="h4 mb-4">Pagini și Design</h2>
      <div class="mb-3">
        <label class="form-label">Tipuri de pagini necesare</label>
        [multichoice page-types class:form-select multiple "Despre noi" "Servicii" "Pagina Individuala Serviciu" "Portofoliu" "Pagina Individuala Proiect Portofoliu" "Magazin (Shop)" "Produse" "Pagina individuala de produs" "Blog (Noutăți)" "Contact" ]
      </div>
      <div class="mb-3">
        <label class="form-label d-block">Servicii de design</label>
        [checkbox design-services class:form-check-box "Creare logo" "Design personalizat" "Manual de branding"]
      </div>
      <div class="mb-3">
        <label class="form-label">Descriere proiect</label>
        [textarea your-design-preferences rows:4 class:form-control placeholder "Descrieți website-ul dorit în câteva cuvinte (preferinte culori, site-uri care vă plac, funcționalităti)"]
      </div>
      <button type="button" class="btn btn-secondary go-to-step" data-target-step="#step-1" data-validate="false">Pasul Anterior</button>
      <button type="button" class="btn btn-primary go-to-step" data-target-step="#step-3">Următorul Pas</button>
    </div>
  </div>
  
  <!-- Pasul 3: Buget și Termen -->
  <div class="step" id="step-3">
    <div class="container">
      <h2 class="h3">Buget și Termen</h2>
      <div class="mb-3">
        <label class="form-label">Bugetul dvs. estimativ</label>
        [range_slider budget-range min:400 max:21000 step:250 value:800]
        <p id="package-description">
        <span  class="text-primary fw-bold d-block h4 pt-3 mb-0">Buget de <span id="selected-amount">500</span> €.</span>
        <span id="selected-package">Site de bază</span>
    </p>
      </div>
      <div class="mb-3">
        <label class="form-label">Data limită pentru finalizare (opțional)</label>
        [select your-deadline class:form-select first_as_label  "Fără termen limită" "Urgent - În următoarele 2 săptămâni" "Rapid - in 4 săptămâni" "Normal, În următoarele 8 săptămâni"]
      </div>
       <div class="mb-3">
        <label class="form-label">Orice alte detalii sau cerințe specifice</label>
        [textarea your-additional-info rows:3 class:form-control placeholder "Orice alte detalii sau cerințe specifice"]
      </div>     
   <button type="submit" class="btn btn-primary wpcf7-submit">Solicită o ofertă</button>
    </div>
  </div>
<!-- Step 4: Final Step - Success Message -->
<div class="step" id="final-step">
  <div class="final-message">
    <h2  class="h4 mb-4">Solicitare trimisă cu succes!</h2>
    <!-- Placeholder for CF7 response output -->
    <div class="wpcf7-response-output"></div>
    <button type="button" class="btn btn-primary go-to-step" data-target-step="#step-1">Back to Start</button>
  </div>
</div>',
  '_mail' => 
  array (
    'active' => true,
    'subject' => 'New Website Creation Request: "[your-subject]"',
    'sender' => 'Your Website Name <no-reply@yourdomain.com>',
    'recipient' => 'info@yourdomain.com',
    'body' => 'You have received a new request for website creation.

Details:
Name: [your-name]
Email: [your-email]
Project Type: [project-type]
Desired Features: [website-features]
Design Services: [design-services]
Design Preferences: [your-design-preferences]
Estimated Budget: [your-budget]
Deadline: [your-deadline]
Additional Information: [your-additional-info]
GDPR Compliance: [gdpr]

-- 
This email was sent from the contact form on Your Website Name.',
    'additional_headers' => 'Reply-To: [your-email]',
    'attachments' => '',
    'use_html' => false,
    'exclude_blank' => false,
  ),
  '_mail_2' => 
  array (
    'active' => true,
    'subject' => 'Confirmarea solicitării dumneavoastră de creare a site-ului web',
    'sender' => 'Numele site-ului dumneavoastră <no-reply@yourdomain.com>',
    'recipient' => '[your-email]',
    'body' => 'Bună [your-name],

Vă mulțumim că ne-ați contactat pentru solicitarea de creare a site-ului web. Am primit solicitarea dumneavoastră cu următoarele detalii:

Tipul proiectului: [project-type]
Funcționalități dorite: [website-features]
Servicii de design: [design-services]
Preferințe de design: [your-design-preferences]
Buget estimativ: [your-budget]
Data limită pentru finalizare: [your-deadline]
Informații suplimentare: [your-additional-info]
Conformitate GDPR: [gdpr]

Vom revizui solicitarea dumneavoastră și vă vom contacta cât mai curând posibil pentru a discuta următorii pași.

Cu cele mai bune urări,
Echipa Mediabit

-- 
Acesta este un răspuns automat. Vă rugăm să nu răspundeți direct la acest e-mail.',
    'additional_headers' => '',
    'attachments' => '',
    'use_html' => false,
    'exclude_blank' => false,
  ),
  '_messages' => 
  array (
    'mail_sent_ok' => 'Vă mulțumim pentru mesajul dvs. A fost trimis.',
    'mail_sent_ng' => 'A apărut o eroare la trimiterea mesajului dvs. Vă rugăm să încercați din nou mai târziu.',
    'validation_error' => 'Unul sau mai multe câmpuri conțin erori. Vă rugăm să verificați și să încercați din nou.',
    'spam' => 'A apărut o eroare la trimiterea mesajului dvs. Vă rugăm să încercați din nou mai târziu.',
    'accept_terms' => 'Trebuie să acceptați termenii și condițiile înainte de a trimite mesajul.',
    'invalid_required' => 'Vă rugăm să completați acest câmp.',
    'invalid_too_long' => 'Acest câmp conține un text prea lung.',
    'invalid_too_short' => 'Acest câmp conține un text prea scurt.',
    'upload_failed' => 'A apărut o eroare necunoscută la încărcarea fișierului.',
    'upload_file_type_invalid' => 'Nu aveți permisiunea de a încărca fișiere de acest tip.',
    'upload_file_too_large' => 'Fișierul încărcat este prea mare.',
    'upload_failed_php_error' => 'A apărut o eroare la încărcarea fișierului.',
    'invalid_date' => 'Vă rugăm să introduceți o dată în formatul AAAA-LL-ZZ.',
    'date_too_early' => 'Acest câmp conține o dată prea timpurie.',
    'date_too_late' => 'Acest câmp conține o dată prea târzie.',
    'invalid_number' => 'Vă rugăm să introduceți un număr.',
    'number_too_small' => 'Acest câmp conține un număr prea mic.',
    'number_too_large' => 'Acest câmp conține un număr prea mare.',
    'quiz_answer_not_correct' => 'Răspunsul la chestionar nu este corect.',
    'invalid_email' => 'Vă rugăm să introduceți o adresă de email.',
    'invalid_url' => 'Vă rugăm să introduceți un URL.',
    'invalid_tel' => 'Vă rugăm să introduceți un număr de telefon.',
  ),
);
