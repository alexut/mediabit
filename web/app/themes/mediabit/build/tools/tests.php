<?php
// if $_GET is set to test=true the contract generation
if ( isset( $_GET['test'] ) && $_GET['test'] == 'true' ) {
    $contract = new Mediabit\Dashboard\Offer( 1224 );
    // generate the contract
    $contract->generateInvoice();
    // $contract->generateAnnex();

    // $annex = new Mediabit\Dashboard\Annex( 328 );
    // $annex->generateAnnex();
    // $annex->createInvoice();
    
    // // $annex = new Mediabit\Dashboard\Invoice( 321 );
    // $annex->generateInvoice();
    // $workflow->createInvoice();
    // var_dump( get_field( 'invoice_details', 241 ));
}
 

// string(92) "C:\laragon\www\mediabit/web/app/themes/mediabit/build/dashboard/view/123_psymep_anexa_4.docx"
// string(71) "C:\laragon\www\mediabit/web/app/uploads/2023/07/133-psymep-ianuarie.pdf"
