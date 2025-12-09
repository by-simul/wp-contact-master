<?php 

class WPCM_Contact_DB {

    private $table_name;

    public function __construct(){
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'wpcm_submissions';
    }

    public function create_table() {

        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            message text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    public function insert_submission($name, $email, $message){
        global $wpdb;

        return $wpdb->insert(
            $this->table_name,
            [
                'name'    => $name,
                'email'   => $email,
                'message' => $message
            ],
            ['%s', '%s', '%s']
        );
    }

    public function get_all_submissions() {
        global $wpdb;

        return $wpdb->get_results(
            "SELECT * FROM {$this->table_name} ORDER BY id DESC"
        );
    }

    public function delete_submission($id) {
        global $wpdb;

        return $wpdb->delete(
            $this->table_name,
            ['id' => $id],   // WHERE id = ?
            ['%d']           // integer format
        );
    }


    
}
