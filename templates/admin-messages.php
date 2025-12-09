<div class="wrap">
    <h1 class="wp-heading-inline">All Contact Messages</h1>
    <hr class="wp-header-end">

    <p>Below are all the messages submitted from your contact form.</p>

    <table class="widefat fixed striped">
        <thead>
            <tr>
                <th width="20%">Name</th>
                <th width="25%">Email</th>
                <th width="40%">Message</th>
                <th width="15%">Date</th>
            </tr>
        </thead>

        <tbody>
            <?php if ( isset($messages) && ! empty($messages) ) : ?>
                
                <?php foreach ($messages as $msg) : ?>
                    <tr>
                        <td><?php echo esc_html($msg->name); ?></td>
                        <td><?php echo esc_html($msg->email); ?></td>
                        <td><?php echo esc_html($msg->message); ?></td>
                        <td><?php echo esc_html($msg->created_at); ?></td>
                    </tr>
                <?php endforeach; ?>

            <?php else : ?>
                <tr>
                    <td colspan="4" style="text-align:center; padding:20px;">
                        No messages found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
