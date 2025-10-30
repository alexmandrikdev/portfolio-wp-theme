// eslint-disable-next-line no-undef
jQuery( document ).ready( function ( $ ) {
	let mediaUploader;

	$( document ).on( 'click', '.technology-icon-upload', function ( e ) {
		e.preventDefault();

		if ( mediaUploader ) {
			mediaUploader.open();
			return;
		}

		mediaUploader = wp.media( {
			title: 'Select Icon',
			button: {
				text: 'Use this icon',
			},
			multiple: false,
			library: {
				type: 'image',
			},
		} );

		const button = $( this );
		const wrapper = button.closest( '.term-icon-wrap, td' );

		mediaUploader.on( 'select', function () {
			const attachment = mediaUploader
				.state()
				.get( 'selection' )
				.first()
				.toJSON();

			const input = wrapper.find( '#technology-icon' );
			const preview = wrapper.find( '.technology-icon-preview img' );

			input.val( attachment.id );
			preview.attr( 'src', attachment.url ).show();

			const removeBtn = wrapper.find( '.technology-icon-remove' );
			removeBtn.show();
		} );

		mediaUploader.open();
	} );

	$( document ).on( 'click', '.technology-icon-remove', function ( e ) {
		e.preventDefault();

		const button = $( this );
		const wrapper = button.closest( '.term-icon-wrap, td' );
		const preview = wrapper.find( '.technology-icon-preview img' );
		const input = wrapper.find( '#technology-icon' );

		input.val( '' );
		preview.attr( 'src', '' ).hide();
		button.hide();
	} );
} );
