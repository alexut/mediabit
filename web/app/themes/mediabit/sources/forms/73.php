<?php
$form_data = array (
  'ID' => 73,
  'post_name' => 'testare',
  'post_content' => '',
  'post_title' => 'Formular Creare Website',
  'post_date' => '2024-03-21 12:00:00',
  '_form' => '<!-- Pasul 1: Informații Generale -->
  <div class="step active" id="step-1">
    <div class="container">
      <h2  class="h4 mb-4">Informații Generale</h2>
      <div class="row">
        <div class="col-lg-6">
        <div class="mb-3">
          <label class="form-label">Nume și prenume.</label>
            [text* your-name class:form-control placeholder "Popescu Ion"]
          </div>
        </div>
        <div class="col-lg-6">
          <div class="mb-3">
            <label class="form-label">Adresa de email</label>
            [email* your-email class:form-control placeholder "Adresa de email"]
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
            [select* domeniu-activitate class:form-select first_as_label 
            "Alege-ti o optiune|"
            "IT & Tech|it-tech" 
            "Medicina & Farma|medicina-farma" 
            "Sport & Fitness|sport-fitness" 
            "Turism|turism" 
            "Educație|educatie" 
            "ONG|ong" 
            "Altele|altele"]
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
        <!-- Default page-types field -->
    <div id="page-types-default-wrapper" class="page-types-field" style="display:block;">
        [multichoice page-types-default class:form-select multiple 
            "Acasă" 
            "Despre noi" 
            "Servicii" 
            "Pagini individuale servicii"
            "Portofoliu" 
            "Proiecte"
            "Magazin (Shop)"
            "Produse"
            "Blog (Noutăți)" 
            "Contact"
            "FAQ"
            "Întrebări frecvente"
            "Galerie foto/video"]
    </div>

    <!-- IT & Tech page-types field -->
    <div id="page-types-it-tech-wrapper" class="page-types-field" style="display:none;">
        [multichoice page-types-it-tech class:form-select multiple 
            "Acasă" 
            "Despre noi" 
            "Servicii IT" 
            "Pagini individuale servicii"
            "Studii de caz" 
            "Blog tehnic" 
            "Proiecte"
            "Parteneriate"
            "Magazin de produse tech"
            "Suport tehnic"
            "Testimoniale"
            "Contact"
            "Întrebări frecvente"
            "Galerie video"]
    </div>

    <!-- Medicină & Farma page-types field -->
    <div id="page-types-medicina-farma-wrapper" class="page-types-field" style="display:none;">
        [multichoice page-types-medicina-farma class:form-select multiple 
            "Acasă" 
            "Clinică" 
            "Servicii medicale" 
            "Pagini individuale servicii"
            "Echipă medicală" 
            "Blog (Noutăți)"
            "Testimoniale"
            "Programări online"
            "Tarife"
            "Cercetare și dezvoltare"
            "Parteneriate"
            "Contact"
            "Întrebări frecvente"]
    </div>
    
    <!-- Sport & Fitness page-types field -->
    <div id="page-types-sport-fitness-wrapper" class="page-types-field" style="display:none;">
        [multichoice page-types-sport-fitness class:form-select multiple 
            "Acasă" 
            "Despre noi" 
            "Programe fitness"
            "Pagini individuale programe"
            "Antrenori"
            "Transformări"
            "Testimoniale"
            "Blog (Noutăți)"
            "Programări sesiuni"
            "Program orar 0zilnic"
            "Tarife"
            "Parteneriate"
            "Contact"
            "Galerie foto/video"]
    </div>

    <!-- Turism page-types field -->
    <div id="page-types-turism-wrapper" class="page-types-field" style="display:none;">
        [multichoice page-types-turism class:form-select multiple 
            "Acasă" 
            "Despre noi" 
            "Destinații"
            "Oferte speciale"
            "Testimoniale"
            "Galerie foto/video"
            "Blog (Sfaturi de călătorie)"
            "Ghiduri turistice"
            "Parteneriate"
            "Magazin suveniruri"
            "Rezervări"
            "Contact"
            "Întrebări frecvente"]
    </div>
    
    <!-- Educație page-types field -->
    <div id="page-types-educatie-wrapper" class="page-types-field" style="display:none;">
        [multichoice page-types-educatie class:form-select multiple 
            "Acasă" 
            "Despre noi" 
            "Cursuri"
            "Facultate"
            "Succes alumni"
            "Blog (Noutăți)"
            "Galerie evenimente"
            "Programări cursuri"
            "Tarife"
            "Parteneriate"
            "Resurse online"
            "Contact"
            "Galerie foto/video"]
    </div>
    
    <!-- ONG page-types field -->
    <div id="page-types-ong-wrapper" class="page-types-field" style="display:none;">
        [multichoice page-types-ong class:form-select multiple 
            "Acasă" 
            "Despre noi" 
            "Misiunea noastră"
            "Proiecte"
            "Implică-te"
            "Blog (Noutăți)"
            "Testimoniale"
            "Parteneriate"
            "Evenimente"
            "Resurse"
            "Raport anual"
            "Contact"
            "Galerie foto"]
    </div>

    <!-- Altele page-types field -->
    <div id="page-types-altele-wrapper" class="page-types-field" style="display:none;">
        [multichoice page-types-altele class:form-select multiple 
            "Acasă" 
            "Despre noi" 
            "Servicii"
            "Portofoliu"
            "Blog (Noutăți)"
            "Testimoniale"
            "Parteneriate"
            "Galerie"
            "Resurse"
            "Magazin"
            "Contact"
            "Întrebări frecvente"
            "Galerie video"]
    </div>      </div>
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
        <label class="form-label">Bugetul estimativ</label>
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
    'sender' => 'Mediabit <no-reply@mediabit.ro>',
    'recipient' => 'hello@mediabit.ro',
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
    'subject' => 'Confirmarea solicitării dumneavoastră pentru crearea unui site web',
    'sender' => 'Mediabit <hello@mediabit.ro>',
    'recipient' => '[your-email]',
    'body' => 'Bună [your-name],

Vă mulțumim că ne-ați contactat. Am primit solicitarea dumneavoastră pentru crearea unui site web cu următoarele detalii:

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
    'mail_sent_ok' => 'Vă mulțumim pentru cererea dumneavoastră. Vă vom contacta cât mai curând posibil.',
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
