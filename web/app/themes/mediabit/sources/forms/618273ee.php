<?php
$form_data = array (
  'ID' => 11,
  'post_name' => 'test21131',
  'post_content' => '',
  'post_title' => 'test21131',
  'post_date' => '2024-02-24 08:13:54',
  '_hash' => '618273ee3bebd0260826781d901f804b',
  '_form' => '<!-- Step 1: Personal Details -->
  <div class="step active" id="step-1">
    <div class="row g-3 mb-3">
      <div class="col-lg-6">
      <label class="form-label d-block">Age</label>
  [text someber "some test"]
      </div>
      <div class="col-lg-6">
      <label class="form-label d-block">Salution <span class="text-danger">*</span></label>
      [checkbox* salution use_label_element exclusive "Mrs." "Mr." "Other"]
      </div>
      <div class="col-md-6">
      <label class="form-label d-block">I\'m interested in</label>
      [radio interests use_label_element default:1 "Nothing" "Sports" "Cooking"]
      </div>
      <div class="col-md-6">
      <label class="form-label d-block">Date</label>
      [date date class:form-control]
      </div>
    </div>
    <button type="button" class="go-to-step" data-target-step="#step-2">Next</button>
  </div>
  
  <!-- Step 2: Preferences and Contact -->
  <div class="step" id="step-2">
    <div class="row g-3 mb-3">
  
    <div class="col-md-6">
      <label class="form-label">First name <span class="text-danger">*</span></label>
      [text* first-name class:form-control placeholder "Enter your first name"]
    </div>
  
    <div class="col-md-6">
      <label class="form-label">Last name <span class="text-danger">*</span></label>
      [text* last-name class:form-control placeholder "Enter your last name"]
    </div>
  
    <div class="col-md-6">
      <label class="form-label">Email <span class="text-danger">*</span></label>
      [email* your-email class:form-control placeholder "Enter a valid email address"]
    </div>
  
    <div class="col-md-6">
      <label class="form-label">Where are you from? <span class="text-danger">*</span></label>
      [select* region class:form-select first_as_label "Choose region" "Asia" "Africa" "Europe" "North America" "South America" "Australia/Ocania"]
    </div>
    </div>
    <button type="button" class="go-to-step" data-target-step="#step-1" data-validate="false">Previous</button>
    <button type="button" class="go-to-step" data-target-step="#step-3">Next</button>
  </div>
  
  <!-- Step 3: Additional Information and Submission -->
  <div class="step" id="step-3">
    <div class="row g-3 mb-3">
      <div class="col-12">
      <label class="form-label">File upload (.jpg, .jpeg, .png, max-size 3MB)</label>
      [file file-upload class:form-control id:form-file limit:3mb filetypes:jpg|jpeg|png]
      </div>

      <div class="col-12">
        <label class="form-label">Subject</label>
        [text your-subject class:form-control placeholder "Quick summary"]
      </div>

      <div class="col-12">
        <label class="form-label">Message <span class="text-danger">*</span></label>
        [textarea* message class:form-control placeholder "Your message to us"]
      </div>

      <div class="col-12">
        [acceptance newsletter optional] Newsletter [/acceptance]
      </div>

      <div class="col-12">
        [acceptance terms use_label_element]I have read the <a href="#" target="_blank">privacy policy</a> note. I consent to the electronic storage and processing of my entered data to answer my request. Note: You can revoke your consent at any time in the future by emailing <a href="mailto:mail@yourdomain.com">mail@yourdomain.com</a>.[/acceptance]
      </div>

      <div class="col-12">
        <button type="button" class="go-to-step" data-target-step="#step-2"  data-validate="false">Previous</button>
        <button type="submit" class="btn btn-primary wpcf7-submit w-100" disabled="disabled">Send Message</button>
      </div>
    </div>
  </div>
  <!-- Step 4: Final Step - Success Message -->
<div class="step" id="final-step" style="display: none;">
    <div class="final-message">
        <h2>Submission Successful</h2>
        <!-- Placeholder for CF7 response output -->
        <div class="wpcf7-response-output"></div>
        <button type="button" class="go-to-step" data-target-step="#step-1" data-validate="false">Back to Start</button>
    </div>
</div>',
  '_mail' => 
  array (
    'active' => true,
    'subject' => '[_site_title] "[your-subject]"',
    'sender' => '[_site_title] <wordpress@mediabit.ro.test>',
    'recipient' => '[_site_admin_email]',
    'body' => 'Inquiry contact form on [_site_title] from [salution] [first-name] [last-name].

    Contact details:
    
    Salution: [salution]
    First name: [first-name]
    Last name: [last-name]
    Age: [age]
    Date: [date]
    Interests: [interests]
    Email: [your-email]
    Region: [region]
    Subject: [your-subject]
    
    Message:
    [message]
    
    [newsletter]
    
    [terms]
       
    -- 
    This email was sent from a contact form on [_site_title].
    
    Company name
    Street
    City
    
    Email: mail@yourdomain.com
    Phone: 1234567
    -- 
    This is a notification that a contact form was submitted on your website ([_site_title] [_site_url]).',
    'additional_headers' => 'Reply-To: [your-email]',
    'attachments' => '',
    'use_html' => false,
    'exclude_blank' => false,
  ),
  '_mail_2' => 
  array (
    'active' => true,
    'subject' => '[_site_title] "[your-subject]"',
    'sender' => '[_site_title] <wordpress@mediabit.ro.test>',
    'recipient' => '[your-email]',
    'body' => 'Hello [salution] [first-name] [last-name],

    thank you for contacting us. We will answer as soon as possible.
    
-- 
This email is a receipt for your contact form submission on our website ([_site_title] [_site_url]) in which your email address was used. If that was not you, please ignore this message.',
    'additional_headers' => 'Reply-To: [_site_admin_email]',
    'attachments' => '',
    'use_html' => false,
    'exclude_blank' => false,
  ),
  '_messages' => 
  array (
    'mail_sent_ok' => 'Thank you for your message bib bim. It has been sent.',
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
