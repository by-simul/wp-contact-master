<?php

class WPCM_Contact_Frontend
{

    private $notice = '';

    public function init()
    {
        add_shortcode('wpcm_form', [$this, 'render_form']);
        add_action('init', [$this, 'handle_form']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets()
    {
        wp_enqueue_style(
            'wpcm-frontend',
            WPCM_URL . 'assets/css/style.css',
            [],
            '1.0'
        );
    }

    public function render_form($atts = [])
    {
        $atts = shortcode_atts([
            'id' => ''
        ], $atts);

        $form_id = sanitize_text_field($atts['id']);
        $shortcodes = get_option('wpcm_custom_shortcodes', []);

        if (!isset($shortcodes[$form_id])) {
            return "<p style='color:red;'>Invalid shortcode ID!</p>";
        }

        $sc = $shortcodes[$form_id];
        $fields = $sc['fields'];
        $button = $sc['button'];

        ob_start();
        ?>

        <div class="wpcm-form-wrapper">

            <?php if (!empty($this->notice)): ?>
                <div class="wpcm-notice"><?php echo wp_kses_post($this->notice); ?></div>
            <?php endif; ?>

            <form method="post" class="wpcm-form">
                <?php wp_nonce_field('wpcm_form_action', 'wpcm_form_nonce'); ?>

                <!-- ⭐ MUST HAVE: Shortcode ID passed to POST -->
                <input type="hidden" name="wpcm_form_id" value="<?php echo esc_attr($form_id); ?>">

                <?php if (in_array('name', $fields)): ?>
                    <p class="wpcm-field">
                        <label>Your Name</label>
                        <input type="text" name="wpcm_name" required>
                    </p>
                <?php endif; ?>

                <?php if (in_array('email', $fields)): ?>
                    <p class="wpcm-field">
                        <label>Your Email</label>
                        <input type="email" name="wpcm_email" required>
                    </p>
                <?php endif; ?>

                <?php if (in_array('message', $fields)): ?>
                    <p class="wpcm-field">
                        <label>Message</label>
                        <textarea name="wpcm_message" required></textarea>
                    </p>
                <?php endif; ?>

                <?php if (in_array('phone', $fields)): ?>
                    <p class="wpcm-field">
                        <label>Phone</label>
                        <input type="text" name="wpcm_phone">
                    </p>
                <?php endif; ?>

                <?php if (in_array('subject', $fields)): ?>
                    <p class="wpcm-field">
                        <label>Subject</label>
                        <input type="text" name="wpcm_subject">
                    </p>
                <?php endif; ?>

                <p class="wpcm-actions">
                    <button type="submit" name="wpcm_submit" class="wpcm-btn">
                        <?php echo esc_html($button); ?>
                    </button>
                </p>

            </form>

        </div>

        <?php
        return ob_get_clean();
    }


    public function handle_form()
    {
        if (!isset($_POST['wpcm_submit'])) {
            return;
        }

        if (
            !isset($_POST['wpcm_form_nonce']) ||
            !wp_verify_nonce($_POST['wpcm_form_nonce'], 'wpcm_form_action')
        ) {
            $this->notice = '<div class="wpcm-error">Security check failed.</div>';
            return;
        }

        // ⭐ Load shortcode settings
        $form_id = sanitize_text_field($_POST['wpcm_form_id']);
        $shortcodes = get_option('wpcm_custom_shortcodes', []);

        if (!isset($shortcodes[$form_id])) {
            $this->notice = '<div class="wpcm-error">Invalid form.</div>';
            return;
        }

        $sc = $shortcodes[$form_id];
        $fields = $sc['fields'];

        // ⭐ Collect dynamic fields
        $data = [];

        if (in_array('name', $fields)) {
            $data['name'] = sanitize_text_field($_POST['wpcm_name']);
        }

        if (in_array('email', $fields)) {
            $data['email'] = sanitize_email($_POST['wpcm_email']);
        }

        if (in_array('message', $fields)) {
            $data['message'] = sanitize_textarea_field($_POST['wpcm_message']);
        }

        if (in_array('phone', $fields)) {
            $data['phone'] = sanitize_text_field($_POST['wpcm_phone']);
        }

        if (in_array('subject', $fields)) {
            $data['subject'] = sanitize_text_field($_POST['wpcm_subject']);
        }

        // ⭐ Dynamic Validation
        foreach ($fields as $f) {
            if (empty($data[$f])) {
                $this->notice = "<div class='wpcm-error'>Field '$f' is required.</div>";
                return;
            }
        }

        // ⭐ Insert into DB (extend if phone/subject needed)
        require_once WPCM_PATH . 'includes/class-db.php';
        $db = new WPCM_Contact_DB();
        $db->insert_submission($data['name'], $data['email'], $data['message']);

        $success_message = $sc['success'];

        wp_redirect(add_query_arg('wpcm_msg', urlencode($success_message), wp_get_referer()));
        exit;
    }
}
