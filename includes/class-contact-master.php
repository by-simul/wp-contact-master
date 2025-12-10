<?php 

class WPCM_Contact_Master {
    public function init(){
        // Admin
        require_once WPCM_PATH . 'includes/class-admin.php';
        $admin = new WPCM_Contact_Admin();
        $admin->init();

        //Frontend
        require_once WPCM_PATH . 'includes/class-frontend.php';
        $frontend = new WPCM_Contact_Frontend();
        $frontend->init();

        // DB
        require_once WPCM_PATH . 'includes/class-db.php';
        $db = new WPCM_Contact_DB();
        $db->create_table();
    }
}







?>