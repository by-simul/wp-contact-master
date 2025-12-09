<div class="wrap">

    <?php if (isset($_GET['deleted'])) : ?>
        <div class="notice notice-success is-dismissible">
            <p>Message deleted successfully.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['bulk_deleted'])) : ?>
        <div class="notice notice-success is-dismissible">
            <p>Selected messages deleted successfully.</p>
        </div>
    <?php endif; ?>


    <h1 class="wp-heading-inline">All Contact Messages</h1>
    <hr class="wp-header-end">

    <p>Below are all the messages submitted from your contact form.</p>

    <form method="post" action="">

        <?php wp_nonce_field('wpcm_bulk_delete_action', 'wpcm_bulk_delete_nonce'); ?>

        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th width="5%">
                        <input type="checkbox" id="wpcm-select-all">
                    </th>
                    <th width="20%">Name</th>
                    <th width="25%">Email</th>
                    <th width="40%">Message</th>
                    <th width="10%">Date</th>
                    <th width="10%">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if ( isset($messages) && ! empty($messages) ) : ?>

                    <?php foreach ($messages as $msg) : ?>

                        <?php
                        $delete_url = wp_nonce_url(
                            admin_url('admin.php?page=wpcm_messages&action=delete&id=' . $msg->id),
                            'wpcm_delete_message'
                        );
                        ?>

                        <tr>
                            <td>
                                <input type="checkbox" name="selected_ids[]" value="<?php echo $msg->id; ?>">
                            </td>

                            <td><?php echo esc_html($msg->name); ?></td>
                            <td><?php echo esc_html($msg->email); ?></td>
                            <td><?php echo esc_html($msg->message); ?></td>
                            <td><?php echo esc_html($msg->created_at); ?></td>

                            <td>
                                <a href="<?php echo $delete_url; ?>"
                                   onclick="return confirm('Are you sure you want to delete this message?');"
                                   style="color:red;">
                                    Delete
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else : ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:20px;">
                            No messages found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <p>
            <input type="submit" name="bulk_delete" class="button button-danger" 
                   value="Delete Selected"
                   onclick="return confirm('Are you sure you want to delete selected messages?');">
        </p>

    </form>

</div>


<script>
document.getElementById('wpcm-select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
