jQuery(document).ready(function ($) {
	var $background_input_media_src = $('#login-page-background-media-src'),
		$background_input_media_id  = $('#login-page-background-media-id'),
		$background_preview         = $('#login-page-background-preview'),
		$background_upload_button   = $('#login-page-background-upload'),
		$background_remove_button   = $('#login-page-background-remove'),
		$form_select                = $('.form-table select'),
		file_frame;

	// Update preview whenever select is changed
	$form_select.change( function() {
		var $select          = $(this),
			preview_prefix   = $select.attr( 'data-preview-prefix' ),
			$selected_option = $select.find('option:selected'),
			selected_value   = $selected_option.val(),
			preview_file     = preview_prefix + selected_value,
			$preview_wrapper = $select.parents('td').find('.option-preview'),
			$preview;

		if( selected_value !== '' ) {
			$preview = $('<img />', {
				src : et_divi_100_js_params.preview_dir_url + preview_file + '.gif'
			});

			$preview_wrapper.css({ 'minHeight' : 182 }).html( $preview );
		} else {
			$preview_wrapper.css({ 'minHeight' : '' }).empty();
		}
	});

	// Check background image status
	if ( $background_input_media_src.val() !== '' ) {
		$background_upload_button.text( et_divi_100_js_params.upload_background_active_text );
		$background_remove_button.show();
	}

	// Upload background image button
	$background_upload_button.on( 'click', function( event ){
		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {

			// Open frame
			file_frame.open();

			return;
		} else {

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: et_divi_100_js_params.media_uploader_title,
				button: {
					text: et_divi_100_js_params.media_uploader_button_text,
				},
				multiple: false  // Set to true to allow multiple files to be selected
			});

			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				// We set multiple to false so only get one image from the uploader
				attachment = file_frame.state().get('selection').first().toJSON();

				// Update input fields
				$background_input_media_src.val( attachment.url );

				$background_input_media_id.val( attachment.id );

				// Update Previewer
				$background_preview.html( $( '<img />', {
					src : attachment.url,
					style : 'max-width: 100%;'
				} ) );

				// Update button text
				$background_upload_button.text( et_divi_100_js_params.upload_background_active_text );
				$background_remove_button.show();
			});

			// Finally, open the modal
			file_frame.open();
		}
	});

	// Remove background image
	$background_remove_button.on( 'click', function( event ) {
		event.preventDefault();

		// Remove input
		$background_input_media_src.val('');
		$background_input_media_id.val('');

		// Remove preview
		$background_preview.empty();

		// Update button text
		$background_upload_button.text( et_divi_100_js_params.upload_background_inactive_text );
		$background_remove_button.hide();
	});
});