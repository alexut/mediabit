<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * UI components for field group meta box
 */
class ACFTC_Field_Group_UI {

    /**
     * Get field group UI HTML
     */
    public static function get_field_group_ui_html($field_group_post_obj) {

        if (!$field_group_post_obj) {
            return '';
        }

        // Create Locations UI object
		$locations_class_name = ACFTC_Core::$class_prefix . 'Locations';
		$locations_ui = new $locations_class_name( $field_group_post_obj );

        // Create parent field group object
		$args = array(
			'field_group_id' => $field_group_post_obj->ID,
			// no location argument included here, only used below
		);
		$parent_field_group = new ACFTC_Group($args);

		// If no fields in field group, display notice. 
        // (Needs to be done at this level because ACFTC Group class is used recursively.)
		if (empty($parent_field_group->fields)) {
            return self::get_empty_field_group_notice_html();
		}

        ob_start();

		// Location select UI (if more than one location)
        echo $locations_ui->get_location_select_html();
		?>

		<div class="acftc-code-container">
			<?php echo $locations_ui->get_locations_code_html($parent_field_group); ?>
		</div>

		<?php 
		if (!empty($locations_ui->location_rules)) {
			echo $locations_ui->get_after_field_group_notice_html();
		}

		return ob_get_clean();

    }

    /**
     * Get notice to be displayed when field group has no fields or has not been published.
     */
    private static function get_empty_field_group_notice_html() {

        ob_start(); ?>

        <div class="acftc-intro-notice">
            <p><?php _e('Create some fields and publish the field group to generate theme code.', 'acf-theme-code'); ?></p>
        </div>

        <?php return ob_get_clean();

    }

}