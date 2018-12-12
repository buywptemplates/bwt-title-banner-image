<?php
/*
 Plugin Name: BWT Title Banner Image
 Plugin URI: http://www.buywptemplates.com/
 Description: Use to update banner and show/hide title of internal pages.
 Author: BWT Themes
 Version: 0.1
 Author URI: http://www.buywptemplates.com/
*/

define( 'BWT_TITLE_BANNER_IMAGE_VERSION', '0.1' );

/** Add side meta box for Banner image **/
function bwt_title_banner_image_add_custom_meta_boxes() {

	// Define the Banner Image for custom post, posts & pages.
	$post_types = array ( 'post', 'page' );
	add_meta_box(
		'bwt_title_banner_image_metabox',
		__( 'Banner Image', 'bwt-title-banner-image' ),
		'bwt_title_banner_image_render_metabox',
		$post_types,
		'side'
	);

}

add_action('add_meta_boxes', 'bwt_title_banner_image_add_custom_meta_boxes');

function bwt_title_banner_image_render_metabox($post_id) {

	wp_nonce_field(basename(__FILE__), 'bwt_title_banner_image_metabox_nonce');

	$bwt_title_banner_image_title_on_off = get_post_meta($post_id->ID, 'bwt_title_banner_image_title_on_off', true);
	$bwt_title_banner_image_title_below_on_off = get_post_meta($post_id->ID, 'bwt_title_banner_image_title_below_on_off', true);
	$bwt_title_banner_image_wp_custom_attachment = get_post_meta($post_id->ID, 'bwt_title_banner_image_wp_custom_attachment', true);
	$banner_url = '';
	if ( isset( $bwt_title_banner_image_wp_custom_attachment ) ) {
		$banner_url = $bwt_title_banner_image_wp_custom_attachment;
	}

	?>
	<div class="meta-wrapper">
		<div>
			<p><strong><?php esc_html_e("Upload Image","bwt-title-banner-image") ?></strong></p>
			<input type="hidden" class="img widefat" name="bwt_title_banner_image_wp_custom_attachment" id="bwt_title_banner_image_wp_custom_attachment" value="<?php echo esc_url( $banner_url ); ?>" />
			<input type="button" class="select-img button button-primary" value="<?php esc_attr_e( 'Upload', 'bwt-title-banner-image' ); ?>" data-uploader_title="<?php esc_attr_e( 'Select Image', 'bwt-title-banner-image' ); ?>" data-uploader_button_text="<?php esc_attr_e( 'Choose Image', 'bwt-title-banner-image' ); ?>" />
			<?php
			$wrap_style = '';
			if ( empty( $banner_url ) ) {
				$wrap_style = ' style="display:none;" ';
			}
			?>
			<div class="custom-theme-preview-wrap" <?php echo $wrap_style; ?>>
				<img src="<?php echo esc_url( $banner_url ); ?>" alt="" />
			</div>
		</div>

		<p><input type="checkbox" name="wp_custom_attachment_remove" id="wp_custom_attachment_remove" value="on" />&nbsp;<?php esc_html_e("Remove banner","bwt-title-banner-image") ?></p>
		<hr />
		<p><input type="checkbox" name="bwt_title_banner_image_title_on_off" id="bwt_title_banner_image_title_on_off" value="on" <?php checked( $bwt_title_banner_image_title_on_off, 'on' ); ?> />&nbsp;<?php esc_html_e("Hide Title above banner","bwt-title-banner-image") ?></p>
		<p><input type="checkbox" name="bwt_title_banner_image_title_below_on_off" id="bwt_title_banner_image_title_below_on_off" value="on" <?php checked( $bwt_title_banner_image_title_below_on_off, 'on' ); ?> />&nbsp;<?php esc_html_e("Show Title below banner","bwt-title-banner-image") ?></p>
	</div><!-- .meta-wrapper -->
	<?php
}

function bwt_title_banner_image_save_custom_meta_data($post_id) {
	if (!isset($_POST['bwt_title_banner_image_metabox_nonce']) || !wp_verify_nonce($_POST['bwt_title_banner_image_metabox_nonce'], basename(__FILE__))) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	$bwt_title_banner_image_title_on_off = ( isset( $_POST['bwt_title_banner_image_title_on_off'] ) && 'on' === $_POST['bwt_title_banner_image_title_on_off'] ) ? 'on' : '';
	update_post_meta($post_id, 'bwt_title_banner_image_title_on_off', $bwt_title_banner_image_title_on_off);

	$bwt_title_banner_image_title_below_on_off = ( isset( $_POST['bwt_title_banner_image_title_below_on_off'] ) && 'on' === $_POST['bwt_title_banner_image_title_below_on_off'] ) ? 'on' : '';
	update_post_meta($post_id, 'bwt_title_banner_image_title_below_on_off', $bwt_title_banner_image_title_below_on_off);

	if ( isset( $_POST['bwt_title_banner_image_wp_custom_attachment'] ) ) {
		$upload = esc_url_raw( $_POST['bwt_title_banner_image_wp_custom_attachment'] );
		update_post_meta($post_id, 'bwt_title_banner_image_wp_custom_attachment', $upload);
	}

	$wp_custom_attachment_remove = ( isset( $_POST['wp_custom_attachment_remove'] ) && 'on' === $_POST['wp_custom_attachment_remove'] ) ? 'on' : '';

	if ( 'on' === $wp_custom_attachment_remove ) {
		// Remove banner image.
		update_post_meta($post_id, 'bwt_title_banner_image_wp_custom_attachment', '');
	}

}

add_action('save_post', 'bwt_title_banner_image_save_custom_meta_data');

function bwt_title_banner_image_metabox_enqueue($hook) {
	if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
		wp_enqueue_style('bwt-title-banner-image-metabox', plugin_dir_url( __FILE__ ) . '/css/admin.css');
		wp_enqueue_script('bwt-title-banner-image-metabox', plugin_dir_url( __FILE__ ) . '/js/admin.js', array('jquery'));
	}
}

add_action('admin_enqueue_scripts', 'bwt_title_banner_image_metabox_enqueue');
