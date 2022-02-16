jQuery( document ).ready( function( $ ) {
	// Instantiates the variable that holds the media library frame.
	let metaImageFrame;

	$( 'body' ).on( 'click', '.add-banner-adsense-image', function( e ) {
		e.preventDefault();

		const block = $( this ).parents( '.banner-adsense-block' );
		const clone = block.find( '.banner-adsense-clone' ).clone();
		block.find( '.banner-adsense-images' ).append( clone.html() );
	} );

	$( 'body' ).on( 'click', '.remove-banner-adsense-image', function( e ) {
		e.preventDefault();
		$( this )
			.parents( '.banner-adsense-image' )
			.remove();

		$( this )
			.parents( '.widget-inside' )
			.find( '.widget-control-save' )
			.prop( 'disabled', false );
	} );

	// Runs when the image button is clicked.
	$( 'body' ).on( 'click', '.media-select-btn', function( e ) {
		e.preventDefault();

		const textField = $( this )
			.parents( '.banner-adsense-image' )
			.find( '.banner-adsense-image-id' );
		const previewImg = $( this )
			.parents( '.banner-adsense-image' )
			.find( '.banner-adsense-image-img' );
		const saveBtn = $( this )
			.parents( '.widget-inside' )
			.find( '.widget-control-save' );

		// Sets up the media library frame
		metaImageFrame = wp.media.frames.metaImageFrame = wp.media( {
			title: metaImage.title,
			button: { text: metaImage.button },
			library: { type: metaImage.type },
		} );

		// Runs when an file is selected.
		metaImageFrame.on( 'select', function() {
			// Grabs the attachment selection and creates a JSON representation of the model.
			const mediaAttachment = metaImageFrame
				.state()
				.get( 'selection' )
				.first()
				.toJSON();

			// Sends the attachment URL to our custom image input field.
			$( textField ).val( mediaAttachment.id );
			//$(textField).data('url', mediaAttachment.url);

			const attachmentUrl = mediaAttachment.sizes.thumbnail.url;
			$( previewImg ).attr( 'src', attachmentUrl );

			saveBtn.prop( 'disabled', false );
		} );

		metaImageFrame.on( 'open', function() {
			const selection = metaImageFrame.state().get( 'selection' );
			const id = $( textField ).val();
			const attachment = wp.media.attachment( id );
			selection.add( attachment ? [ attachment ] : [] );
		} );

		// Opens the media library frame.
		metaImageFrame.open();
	} );
} );
