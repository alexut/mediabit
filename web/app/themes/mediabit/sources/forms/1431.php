<?php
$form_data = array (
  'ID' => 1431,
  'post_name' => 'contact-form',
  'post_content' => '',
  'post_title' => 'Contact Form',
  'post_date' => '2024-08-25 13:36:03',
  '_form' => '<div class="container">
  <div class="row">
    <div class="col-lg-6">
      <div class="mb-3">
        <label class="form-label">Numele și prenumele</label>
        [text* your-name class:form-control placeholder "Numele și prenumele"]
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
        <label class="form-label">Numărul de telefon</label>
        [tel* your-phone class:form-control placeholder "Numărul de telefon"]
      </div>
    </div>
    <div class="col-lg-6">
      <div class="mb-3">
        <label class="form-label">Numele firmei</label>
        [text* your-business-name class:form-control placeholder "Numele firmei"]
      </div>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">Tipul solicitării</label>
    [select* enquiry-type class:form-select placeholder "Alege tipul de solicitare"
      "Suport tehnic"
      "Întrebări generale"
      "Solicitare ofertă"
      "Altceva"]
  </div>
  <div class="mb-3">
    <label class="form-label">Mesajul tău</label>
    [textarea your-message class:form-control rows:4 placeholder "Mesajul tău"]
  </div>
  <button type="submit" class="btn btn-primary wpcf7-submit">Trimite</button>
</div>',
  '_mail' => 
  array (
    'active' => true,
    'subject' => '[_site_title] Contact Form Submission',
    'sender' => '[_site_title] <wordpress@mediabit.ro>',
    'recipient' => '[_site_admin_email]',
    'body' => 'Ați primit un mesaj nou de pe formularul de contact de pe site-ul vostru.

Detalii:
Nume: [your-name]
Email: [your-email]
Telefon: [your-phone]
Numele firmei: [your-business-name]
Tip solicitare: [enquiry-type]

Mesaj:
[your-message]

-- 
Acest mesaj a fost trimis de pe formularul de contact de pe site-ul vostru ([_site_title] [_site_url]).',
    'additional_headers' => 'Reply-To: [your-email]',
    'attachments' => '',
    'use_html' => false,
    'exclude_blank' => false,
  ),
  '_mail_2' => 
  array (
    'active' => false,
    'subject' => '[_site_title] Contact Form Submission',
    'sender' => '[_site_title] <wordpress@mediabit.ro>',
    'recipient' => '[your-email]',
    'body' => 'Vă mulțumim pentru mesajul tău. Am primit solicitarea și îți vom răspunde în cel mai scurt timp posibil.

Mesajul tău:
[your-message]

-- 
Acest email este o confirmare a mesajului trimis prin formularul de contact de pe site-ul nostru ([_site_title] [_site_url]). Dacă nu ai trimis acest mesaj, te rugăm să ignori acest email.',
    'additional_headers' => 'Reply-To: [_site_admin_email]',
    'attachments' => '',
    'use_html' => false,
    'exclude_blank' => false,
  ),
  '_messages' => 
  array (
    'mail_sent_ok' => 'Mulțumim pentru mesaj. A fost trimis cu succes.',
    'mail_sent_ng' => 'A apărut o eroare la trimiterea mesajului. Te rugăm să încerci din nou mai târziu.',
    'validation_error' => 'Unul sau mai multe câmpuri conțin erori. Te rugăm să verifici și să încerci din nou.',
    'spam' => 'A apărut o eroare la trimiterea mesajului. Te rugăm să încerci din nou mai târziu.',
    'accept_terms' => 'Trebuie să accepți termenii și condițiile înainte de a trimite mesajul.',
    'invalid_required' => 'Te rugăm să completezi acest câmp.',
    'invalid_too_long' => 'Acest câmp conține un text prea lung.',
    'invalid_too_short' => 'Acest câmp conține un text prea scurt.',
    'upload_failed' => 'A apărut o eroare necunoscută la încărcarea fișierului.',
    'upload_file_type_invalid' => 'Nu ai permisiunea de a încărca fișiere de acest tip.',
    'upload_file_too_large' => 'Fișierul încărcat este prea mare.',
    'upload_failed_php_error' => 'A apărut o eroare la încărcarea fișierului.',
    'invalid_date' => 'Te rugăm să introduci o dată în formatul AAAA-LL-ZZ.',
    'date_too_early' => 'Acest câmp conține o dată prea timpurie.',
    'date_too_late' => 'Acest câmp conține o dată prea târzie.',
    'invalid_number' => 'Te rugăm să introduci un număr.',
    'number_too_small' => 'Acest câmp conține un număr prea mic.',
    'number_too_large' => 'Acest câmp conține un număr prea mare.',
    'quiz_answer_not_correct' => 'Răspunsul la chestionar nu este corect.',
    'invalid_email' => 'Te rugăm să introduci o adresă de email.',
    'invalid_url' => 'Te rugăm să introduci un URL.',
    'invalid_tel' => 'Te rugăm să introduci un număr de telefon.',
  ),
);
