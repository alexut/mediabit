<?php 
add_action('init', function() {
    // Check if the environment is production
    // Path to the db folder relative to the Bedrock root
    $db_folder = ABSPATH . '../../db/';

    // Path to the log file
    $log_file = ABSPATH . '../../db/db_import.log';

    log_message($log_file, "Initiating database import script");

    if (defined('WP_ENV') && WP_ENV === 'production') {

        log_message($log_file, "Environment is production");
        // Fetch all SQL files in the db folder
        $sql_files = glob($db_folder . '*.sql');

        // Check if there are any SQL files
        if (!empty($sql_files)) {
            foreach ($sql_files as $sql_file) {
                // Log the start of the import
                log_message($log_file, "Starting import for file: $sql_file");

                // Import the SQL file into the database
                $success = import_sql_file($sql_file, $log_file);

                // If import was successful, delete the file
                if ($success) {
                    if (unlink($sql_file)) {
                        log_message($log_file, "Successfully deleted file: $sql_file");
                    } else {
                        log_message($log_file, "Failed to delete file: $sql_file", 'ERROR');
                    }
                }
            }
        } else {
            log_message($log_file, "No SQL files found in $db_folder");
        }
    }
});

function import_sql_file($file, $log_file) {
    // Ensure WP-CLI is available and use it to import the database
    $command = 'wp db import ' . escapeshellarg($file);
    
    // Execute the command
    exec($command, $output, $return_var);

    if ($return_var === 0) {
        log_message($log_file, "Database imported successfully from $file.");
        return true;
    } else {
        log_message($log_file, "Failed to import database from $file.", 'ERROR');
        return false;
    }
}

function log_message($log_file, $message, $level = 'INFO') {
    $time = date('Y-m-d H:i:s');
    $formatted_message = "[$time] [$level] $message\n";
    
    // Write the message to the log file
    file_put_contents($log_file, $formatted_message, FILE_APPEND);
}
?>