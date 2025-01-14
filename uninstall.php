<?php
// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Clean up all plugin data
require_once plugin_dir_path(__FILE__) . 'includes/repositories/SpcvDatabaseManager.php';
SpcvDatabaseManager::spcvTeardownDatabase();

// Remove plugin options
delete_option('spcv_db_version');
?> 