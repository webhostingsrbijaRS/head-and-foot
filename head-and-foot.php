<?php
/*
 * Plugin Name:       Head & Foot
 * Plugin URI:        https://github.com/webhostingsrbijaRS/head-and-foot/
 * Description:       Allows users to insert custom scripts in the header and footer.
 * Version:           1.0
 * Requires at least: 6.2
 * Requires PHP:      8.0
 * Author:            WebHostingSrbija
 * Author URI:        https://www.webhostingsrbija.rs/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/webhostingsrbijaRS/head-and-foot/
 * Domain Path:       /languages/
 * Text Domain:       head-and-foot
 */

function hfs_load_textdomain() {
    load_plugin_textdomain('head-and-foot', false, basename(dirname(__FILE__)) . '/languages/');
}
add_action('init', 'hfs_load_textdomain');

function hfs_add_settings_page() {
    if (current_user_can('manage_options')) { // Ensure the user is an administrator
        add_menu_page(__('Head & Foot Scripts', 'head-and-foot'), __('Head & Foot', 'head-and-foot'), 'manage_options', 'head-and-foot-scripts', 'hfs_render_settings_page');
    }
}
add_action('admin_menu', 'hfs_add_settings_page');

// Render the settings page
function hfs_render_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'head-and-foot'));
    }

    if (isset($_POST['hfs_save_settings'])) {
        // Check nonce for security
        if (!isset($_POST['hfs_nonce']) || !wp_verify_nonce($_POST['hfs_nonce'], 'hfs_save_settings')) {
            wp_die(__('Nonce verification failed.', 'head-and-foot'));
        }

        update_option('hfs_header', $_POST['hfs_header']);
        update_option('hfs_after_body', $_POST['hfs_after_body']);
        update_option('hfs_before_body_end', $_POST['hfs_before_body_end']);
        update_option('hfs_footer', $_POST['hfs_footer']);
        echo '<div class="updated"><p>' . __('Settings saved.', 'head-and-foot') . '</p></div>';
    }

    ?>
    <div class="wrap">
        <h2><?php _e('Head & Foot Scripts', 'head-and-foot'); ?></h2>
        <form method="post">
            <?php wp_nonce_field('hfs_save_settings', 'hfs_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="hfs_header"><?php _e('Header', 'head-and-foot'); ?></label></th>
                    <td><textarea name="hfs_header" rows="5" cols="50"><?php echo esc_textarea(get_option('hfs_header')); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="hfs_after_body"><?php _e('Just After <body>', 'head-and-foot'); ?></label></th>
                    <td><textarea name="hfs_after_body" rows="5" cols="50"><?php echo esc_textarea(get_option('hfs_after_body')); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="hfs_before_body_end"><?php _e('Just Before </body>', 'head-and-foot'); ?></label></th>
                    <td><textarea name="hfs_before_body_end" rows="5" cols="50"><?php echo esc_textarea(get_option('hfs_before_body_end')); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="hfs_footer"><?php _e('Footer', 'head-and-foot'); ?></label></th>
                    <td><textarea name="hfs_footer" rows="5" cols="50"><?php echo esc_textarea(get_option('hfs_footer')); ?></textarea></td>
                </tr>
            </table>
            <input type="submit" name="hfs_save_settings" value="<?php _e('Save Changes', 'head-and-foot'); ?>" class="button button-primary">
        </form>
    </div>
    <?php
}

// hooks

function hfs_inject_scripts() {
    echo get_option('hfs_header');
}
add_action('wp_head', 'hfs_inject_scripts');

function hfs_inject_after_body() {
    echo get_option('hfs_after_body');
}
add_action('wp_body_open', 'hfs_inject_after_body');

function hfs_inject_before_body_end() {
    echo get_option('hfs_before_body_end');
}
add_action('wp_footer', 'hfs_inject_before_body_end', 5);

function hfs_inject_footer_scripts() {
    echo get_option('hfs_footer');
}
add_action('wp_footer', 'hfs_inject_footer_scripts', 10);
?>
