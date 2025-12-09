<?php 

class WPCM_Contact_Frontend {

    private $notice = ''; // ✅ success / error message store korar jonne

    public function init(){
        add_shortcode('wpcm_form', [$this,'render_form']);
        add_action('init', [$this, 'handle_form']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']); // ✅ CSS load
    }

    // ✅ CSS file enqueue
    public function enqueue_assets() {
        wp_enqueue_style(
            'wpcm-frontend',
            WPCM_URL . 'assets/css/style.css',
            [],
            '1.0'
        );
    }

    public function render_form(){
        ob_start();
        ?>
        <div class="wpcm-form-wrapper">
            
            <?php if ( ! empty( $this->notice ) ) : ?>
                <div class="wpcm-notice">
                    <?php echo wp_kses_post( $this->notice ); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="" class="wpcm-form">

                <?php wp_nonce_field('wpcm_form_action', 'wpcm_form_nonce'); ?>

                <p class="wpcm-field">
                    <label for="wpcm_name">Your Name</label>
                    <input type="text" id="wpcm_name" name="wpcm_name" required>
                </p>

                <p class="wpcm-field">
                    <label for="wpcm_email">Your Email</label>
                    <input type="email" id="wpcm_email" name="wpcm_email" required>
                </p>

                <p class="wpcm-field">
                    <label for="wpcm_message">Message</label>
                    <textarea id="wpcm_message" name="wpcm_message" rows="4" required></textarea>
                </p>

                <p class="wpcm-actions">
                    <button type="submit" name="wpcm_submit" class="wpcm-btn">
                        Send Message
                    </button>
                </p>

            </form>
        </div>
        <?php
        return ob_get_clean();
    }


   public function handle_form() {

    if (!isset($_POST['wpcm_submit'])) {
        return;
    }

    if (!isset($_POST['wpcm_form_nonce']) ||
        !wp_verify_nonce($_POST['wpcm_form_nonce'], 'wpcm_form_action')) {

        $this->notice = '<div class="wpcm-error">Security check failed.</div>';
        return;
    }

    $name    = sanitize_text_field($_POST['wpcm_name']);
    $email   = sanitize_email($_POST['wpcm_email']);
    $message = sanitize_textarea_field($_POST['wpcm_message']);

    if (empty($name) || empty($email) || empty($message)) {
        $this->notice = '<div class="wpcm-error">Please fill all fields.</div>';
        return;
    }

    if (!is_email($email)) {
        $this->notice = '<div class="wpcm-error">Invalid email address.</div>';
        return;
    }

    require_once WPCM_PATH . 'includes/class-db.php';
    $db = new WPCM_Contact_DB();
    $db->insert_submission($name, $email, $message);

    $success_message = get_option(
        'wpcm_success_message',
        'Thanks! Your message has been sent.'
    );

    // ⭐ Prevent double-submit (VERY IMPORTANT)
    wp_redirect( add_query_arg('wpcm_msg', urlencode($success_message), wp_get_referer()) );
    exit;
}

}
