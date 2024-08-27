<?php
$form_data = array (
  'ID' => 1426,
  'post_name' => 'formular-mentenanta',
  'post_content' => '',
  'post_title' => 'Formular Mentenanță',
  'post_date' => '2024-08-25 11:11:31',
  '_form' => '<!-- Pasul 1: Informații Generale -->
<div class="step active" id="step-1">
    <div class="container">
      <h2  class="h4 mb-4">Informații Generale</h2>
      <div class="row">
        <div class="col-lg-6">
        <div class="mb-3">
          <label class="form-label">Numele și prenume</label>
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

<!-- Pasul 2: Alege Planul și Detalii -->
<div class="step" id="step-2">
  <div class="container">
    <h2 class="h4 mb-4">Alege Planul care ți se potrivește</h2>
    <div class="mb-3">
      <label class="form-label">Selectați planul de mentenanță</label>
      [select* maintenance-plan class:form-select
        "Basic - €25 /lună (facturat lunar)" 
        "Basic - €20 /lună (facturat anual)" 
        "Pro - €100 /lună (facturat lunar)" 
        "Pro - €90 /lună (facturat anual)" 
        "Enterprise - Customizat"]
    </div>
    <div class="mb-3">
      <label class="form-label">Orice alte detalii sau cerințe specifice</label>
      [textarea your-additional-info rows:3 class:form-control placeholder "Orice alte detalii sau cerințe specifice"]
    </div>
    <button type="button" class="btn btn-secondary go-to-step" data-target-step="#step-1" data-validate="false">Pasul Anterior</button>
    <button type="submit" class="btn btn-primary wpcf7-submit">Solicită Planul</button>
  </div>
</div>

<!-- Step 3: Final Step - Success Message -->
<div class="step" id="final-step" style="display:none;">
  <div class="final-message">
    <h2 class="h4 mb-4">Solicitarea a fost trimisă cu succes!</h2>
    <div class="wpcf7-response-output"></div>
    <button type="button" class="btn btn-primary go-to-step" data-target-step="#step-1">Înapoi la început</button>
  </div>
</div>',
  '_mail' => 
  array (
    'active' => true,
    'subject' => 'New Maintenance Plan Request',
    'sender' => 'Mediabit <no-reply@mediabit.ro>',
    'recipient' => 'hello@mediabit.ro',
    'body' => 'You have received a new request for a maintenance plan.

Details:
Name: [your-name]
Email: [your-email]
Domeniu: [domeniu-activitate]
CUI: [cui-firma]
Maintenance Plan: [maintenance-plan]
Additional Information: [your-additional-info]

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
    'subject' => 'Confirmare primire solicitare de mentenanță',
    'sender' => 'hello@mediabit.ro',
    'recipient' => '[your-email]',
    'body' => 'Bună [your-name],

Vă mulțumim că ne-ați contactat pentru solicitarea unui plan de mentenanță. Am primit solicitarea dumneavoastră și vă vom contacta cât mai curând posibil pentru a discuta următorii pași.

Cu cele mai bune urări,
Echipa Mediabit',
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
