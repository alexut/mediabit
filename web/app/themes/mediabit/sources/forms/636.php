<?php
$form_data = array (
  'ID' => 636,
  'post_name' => 'newsletter',
  'post_content' => '',
  'post_title' => 'Newsletter',
  'post_date' => '2024-05-08 14:21:37',
  '_form' => '[email* your-email class:form-control class:py-3 placeholder "Introdu adresa ta de email"]
<button class="btn btn-primary btn-sm" type="submit"><i class="d-md-none bi bi-envelope"></i><span class="d-none d-md-block">Abonare</span></button>',
  '_mail' => 
  array (
    'active' => true,
    'subject' => '[_site_title] "[your-subject]"',
    'sender' => '[_site_title] <wordpress@mediabit.ro.test>',
    'recipient' => '[_site_admin_email]',
    'body' => 'From: [your-name] [your-email]
Subject: [your-subject]

Message Body:
[your-message]

-- 
This is a notification that a contact form was submitted on your website ([_site_title] [_site_url]).',
    'additional_headers' => 'Reply-To: [your-email]',
    'attachments' => '',
    'use_html' => false,
    'exclude_blank' => false,
  ),
  '_mail_2' => 
  array (
    'active' => false,
    'subject' => '[_site_title] "[your-subject]"',
    'sender' => '[_site_title] <wordpress@mediabit.ro.test>',
    'recipient' => '[your-email]',
    'body' => 'Message Body:
[your-message]

-- 
This email is a receipt for your contact form submission on our website ([_site_title] [_site_url]) in which your email address was used. If that was not you, please ignore this message.',
    'additional_headers' => 'Reply-To: [_site_admin_email]',
    'attachments' => '',
    'use_html' => false,
    'exclude_blank' => false,
  ),
  '_messages' => 
  array (
    'mail_sent_ok' => 'Thank you for your message. It has been sent.',
    'mail_sent_ng' => 'There was an error trying to send your message. Please try again later.',
    'validation_error' => 'One or more fields have an error. Please check and try again.',
    'spam' => 'There was an error trying to send your message. Please try again later.',
    'accept_terms' => 'You must accept the terms and conditions before sending your message.',
    'invalid_required' => 'Please fill out this field.',
    'invalid_too_long' => 'This field has a too long input.',
    'invalid_too_short' => 'This field has a too short input.',
    'upload_failed' => 'There was an unknown error uploading the file.',
    'upload_file_type_invalid' => 'You are not allowed to upload files of this type.',
    'upload_file_too_large' => 'The uploaded file is too large.',
    'upload_failed_php_error' => 'There was an error uploading the file.',
    'invalid_date' => 'Please enter a date in YYYY-MM-DD format.',
    'date_too_early' => 'This field has a too early date.',
    'date_too_late' => 'This field has a too late date.',
    'invalid_number' => 'Please enter a number.',
    'number_too_small' => 'This field has a too small number.',
    'number_too_large' => 'This field has a too large number.',
    'quiz_answer_not_correct' => 'The answer to the quiz is incorrect.',
    'invalid_email' => 'Please enter an email address.',
    'invalid_url' => 'Please enter a URL.',
    'invalid_tel' => 'Please enter a telephone number.',
  ),
);
