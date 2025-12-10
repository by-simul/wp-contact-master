<div class="wrap">

    <h1 class="wp-heading-inline">Shortcodes</h1>
    <hr class="wp-header-end">

    <!-- NAV TABS -->
    <h2 class="nav-tab-wrapper">
        <a href="?page=wpcm_shortcodes"
            class="nav-tab <?php echo empty($_GET['tab']) ? 'nav-tab-active' : ''; ?>">
            All Shortcodes
        </a>

        <a href="?page=wpcm_shortcodes&tab=create"
            class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'create') ? 'nav-tab-active' : ''; ?>">
            Create Shortcode
        </a>
    </h2>


    <!-- TAB 1: ALL SHORTCODES -->
    <?php if (empty($_GET['tab'])): ?>

        <h2>Saved Shortcodes</h2>

        <?php
        $shortcodes = get_option('wpcm_custom_shortcodes', []);
        if (!empty($shortcodes)):
        ?>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Shortcode</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($shortcodes as $id => $sc): ?>
                        <tr>
                            <td><?php echo esc_html($sc['name']); ?></td>
                            <td><code>[wpcm_form id="<?php echo $id; ?>"]</code></td>
                            <td>
                                <a href="?page=wpcm_shortcodes&tab=edit&id=<?php echo $id; ?>" class="button">Edit</a>

                                <button class="button"
                                    onclick="navigator.clipboard.writeText('[wpcm_form id=&quot;<?php echo $id; ?>&quot;]')">
                                    Copy
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p>No shortcodes created yet.</p>
        <?php endif; ?>

    <?php endif; ?>



    <!-- TAB 2: CREATE SHORTCODE -->
    <?php if (isset($_GET['tab']) && $_GET['tab'] === 'create'): ?>

        <h2>Create New Shortcode</h2>

        <form method="post">
            <?php wp_nonce_field('wpcm_create_shortcode_action', 'wpcm_create_shortcode_nonce'); ?>

            <p>
                <label><strong>Shortcode Name</strong></label><br>
                <input type="text" name="sc_name" class="regular-text" required>
            </p>

            <h3>Fields to Include</h3>

            <label><input type="checkbox" name="fields[]" value="name" checked> Name</label><br>
            <label><input type="checkbox" name="fields[]" value="email" checked> Email</label><br>
            <label><input type="checkbox" name="fields[]" value="message" checked> Message</label><br>
            <label><input type="checkbox" name="fields[]" value="phone"> Phone</label><br>
            <label><input type="checkbox" name="fields[]" value="subject"> Subject</label><br>

            <p>
                <label><strong>Success Message</strong></label><br>
                <input type="text" name="success_message" class="regular-text" value="Thanks! Your message has been sent.">
            </p>

            <p>
                <label><strong>Button Text</strong></label><br>
                <input type="text" name="button_text" class="regular-text" value="Send Message">
            </p>

            <p>
                <input type="submit" name="create_shortcode" class="button button-primary" value="Create Shortcode">
            </p>

        </form>

    <?php endif; ?>



    <!-- TAB 3: EDIT SHORTCODE -->
    <?php if (isset($_GET['tab']) && $_GET['tab'] === 'edit'): ?>

        <?php
        $id = sanitize_text_field($_GET['id']);
        $shortcodes = get_option('wpcm_custom_shortcodes', []);

        if (!isset($shortcodes[$id])) {
            echo "<div class='notice notice-error'><p>Invalid shortcode ID.</p></div>";
            return;
        }

        $sc = $shortcodes[$id];
        ?>

        <h2>Edit Shortcode</h2>

        <form method="post">
            <?php wp_nonce_field('wpcm_update_shortcode_action', 'wpcm_update_shortcode_nonce'); ?>

            <input type="hidden" name="sc_id" value="<?php echo esc_attr($id); ?>">

            <p>
                <label><strong>Shortcode Name</strong></label><br>
                <input type="text" name="sc_name"
                    class="regular-text"
                    value="<?php echo esc_attr($sc['name']); ?>" required>
            </p>

            <h3>Fields to Include</h3>

            <?php $fields = $sc['fields']; ?>

            <label><input type="checkbox" name="fields[]" value="name" <?php checked(in_array('name', $fields)); ?>> Name</label><br>
            <label><input type="checkbox" name="fields[]" value="email" <?php checked(in_array('email', $fields)); ?>> Email</label><br>
            <label><input type="checkbox" name="fields[]" value="message" <?php checked(in_array('message', $fields)); ?>> Message</label><br>
            <label><input type="checkbox" name="fields[]" value="phone" <?php checked(in_array('phone', $fields)); ?>> Phone</label><br>
            <label><input type="checkbox" name="fields[]" value="subject" <?php checked(in_array('subject', $fields)); ?>> Subject</label><br>

            <p>
                <label><strong>Success Message</strong></label><br>
                <input type="text" name="success_message"
                    class="regular-text"
                    value="<?php echo esc_attr($sc['success']); ?>">
            </p>

            <p>
                <label><strong>Button Text</strong></label><br>
                <input type="text" name="button_text"
                    class="regular-text"
                    value="<?php echo esc_attr($sc['button']); ?>">
            </p>

            <p>
                <input type="submit" name="update_shortcode"
                    class="button button-primary"
                    value="Update Shortcode">
            </p>

        </form>

    <?php endif; ?>

</div>
