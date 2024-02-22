<?php
    
    // Exit if accessed directly.
    defined( 'ABSPATH' ) || exit;

    $footer = new \Mediabit\Templates\Sections\Footer();
    
    echo $footer->render();
    wp_footer(); 

?>
</body>
</html>