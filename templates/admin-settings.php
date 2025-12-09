<div class="wrap">
    <h1>Contact Form Settings</h1>
    <hr>

    <form method="post" action="options.php">
        <?php 
            settings_fields('wpcm_settings_group'); 
            do_settings_sections('wpcm_settings');
        ?>

        <table class="form-table">

            <tr>
                <th scope="row">
                    <label for="wpcm_admin_email">Admin Email</label>
                </th>
                <td>
                    <input type="email" 
                           id="wpcm_admin_email" 
                           name="wpcm_admin_email" 
                           value="<?php echo esc_attr(get_option('wpcm_admin_email')); ?>" 
                           class="regular-text" />
                    <p class="description">Messages will be sent to this email.</p>
                </td>
            </tr>

            <tr>
                <th scope="row">Email Notifications</th>
                <td>
                    <label>
                        <input type="checkbox" 
                               name="wpcm_enable_email" 
                               value="1"
                               <?php checked(1, get_option('wpcm_enable_email')); ?> />
                        Enable email notification when someone submits the form.
                    </label>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="wpcm_success_message">Success Message</label>
                </th>
                <td>
                    <input type="text" 
                           id="wpcm_success_message" 
                           name="wpcm_success_message" 
                           value="<?php echo esc_attr(get_option('wpcm_success_message', 'Thanks! Your message has been sent.')); ?>" 
                           class="regular-text" />
                </td>
            </tr>

        </table>

        <?php submit_button(); ?>
    </form>
</div>
