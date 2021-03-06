<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated quizmaker-message qm-connect">
	<p><strong><?php _e( 'Quizmaker Data Update', 'quizmaker' ); ?></strong> &#8211; <?php _e( 'We need to update your database to the latest version.', 'quizmaker' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_update_quizmaker', 'true', admin_url( 'admin.php?page=qm-settings' ) ) ); ?>" class="qm-update-now button-primary"><?php _e( 'Run the updater', 'quizmaker' ); ?></a></p>
</div>
<script type="text/javascript">
	jQuery( '.qm-update-now' ).click( 'click', function() {
		return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'quizmaker' ) ); ?>' ); // jshint ignore:line
	});
</script>
