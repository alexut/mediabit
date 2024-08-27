<?php
$form_data = array (
  'ID' => 1905,
  'post_name' => 'marketing-activ',
  'post_content' => '',
  'post_title' => 'Marketing Activ',
  'post_date' => '2024-08-26 18:00:27',
  '_form' => '<div class="step active" id="step-1">
    <div class="container">
      <h2 class="h4 mb-4">Informații Generale</h2>
      <div class="row">
        <div class="col-lg-6">
        <div class="mb-3">
          <label class="form-label">Nume și prenume</label>
            [text* your-name class:form-control placeholder "Popescu Ion"]
          </div>
        </div>
        <div class="col-lg-6">
          <div class="mb-3">
            <label class="form-label">Email</label>
            [email* your-email class:form-control placeholder "Email"]
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="mb-3">
            <label class="form-label">Telefon</label>
            [tel* your-phone class:form-control placeholder "Număr de telefon"]
          </div>
        </div>
        <div class="col-lg-6">
          <div class="mb-3">
            <label class="form-label">Domeniu de activitate</label>
            [select* domeniu-activitate class:form-select first_as_label 
            "Alege o opțiune|"
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

<!-- Pasul 2: Selectați Serviciile de Marketing -->
<div class="step" id="step-2">
  <div class="container">
    <h2 class="h4 mb-4">Alege Serviciile de Marketing</h2>
    <div class="mb-3">
      <label class="form-label">Selectează serviciile dorite</label>
      [multichoice marketing-services class:form-select multiple 
        "SEO" 
        "Content Marketing" 
        "Social Media Management" 
        "Email Marketing" 
        "PPC Advertising" 
        "Marketing Hibrid" 
        "Altele"]
    </div>
    <div class="mb-3">
      <label class="form-label">Detalii suplimentare sau cerințe specifice</label>
      [textarea your-additional-info rows:3 class:form-control placeholder "Detalii suplimentare sau cerințe specifice"]
    </div>
    <button type="button" class="btn btn-secondary go-to-step" data-target-step="#step-1" data-validate="false">Pasul Anterior</button>
    <button type="submit" class="btn btn-primary wpcf7-submit">Solicită Serviciile</button>
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
    'subject' => 'New Marketing Services Request',
    'sender' => 'Mediabit <no-reply@mediabit.ro>',
    'recipient' => 'hello@mediabit.ro',
    'body' => 'You have received a new request for marketing services.

Details:
Name: [your-name]
Email: [your-email]
Phone: [your-phone]
Domeniu: [domeniu-activitate]
Requested Services: [marketing-services]
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
    'subject' => 'Confirmare primire solicitare servicii marketing',
    'sender' => 'hello@mediabit.ro',
    'recipient' => '[your-email]',
    'body' => 'Bună [your-name],

Mulțumim că ne-ai contactat pentru solicitarea serviciilor de marketing. Am primit solicitarea ta și te vom contacta cât mai curând pentru a discuta următorii pași.

Toate cele bune,
Echipa Mediabit',
    'additional_headers' => '',
    'attachments' => '',
    'use_html' => false,
    'exclude_blank' => false,
  ),
  '_messages' => 
  array (
    'mail_sent_ok' => 'Mulțumim pentru cererea ta. Te vom contacta cât mai curând.',
    'mail_sent_ng' => 'A apărut o eroare la trimiterea mesajului. Te rugăm să încerci din nou mai târziu.',
    'validation_error' => 'Unul sau mai multe câmpuri conțin erori. Verifică și încearcă din nou.',
    'spam' => 'A apărut o eroare la trimiterea mesajului. Te rugăm să încerci din nou mai târziu.',
    'accept_terms' => 'Trebuie să accepți termenii și condițiile înainte de a trimite mesajul.',
    'invalid_required' => 'Completează acest câmp.',
    'invalid_too_long' => 'Acest câmp conține un text prea lung.',
    'invalid_too_short' => 'Acest câmp conține un text prea scurt.',
    'upload_failed' => 'A apărut o eroare necunoscută la încărcarea fișierului.',
    'upload_file_type_invalid' => 'Nu ai permisiunea de a încărca fișiere de acest tip.',
    'upload_file_too_large' => 'Fișierul încărcat este prea mare.',
    'upload_failed_php_error' => 'A apărut o eroare la încărcarea fișierului.',
    'invalid_date' => 'Introdu o dată în formatul AAAA-LL-ZZ.',
    'date_too_early' => 'Acest câmp conține o dată prea timpurie.',
    'date_too_late' => 'Acest câmp conține o dată prea târzie.',
    'invalid_number' => 'Introdu un număr.',
    'number_too_small' => 'Acest câmp conține un număr prea mic.',
    'number_too_large' => 'Acest câmp conține un număr prea mare.',
    'quiz_answer_not_correct' => 'Răspunsul la chestionar nu este corect.',
    'invalid_email' => 'Introdu o adresă de email.',
    'invalid_url' => 'Introdu un URL.',
    'invalid_tel' => 'Introdu un număr de telefon.',
  ),
);
?>