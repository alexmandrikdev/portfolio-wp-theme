import { __ } from '@wordpress/i18n';
import { BaseControl, Button } from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';

const MediaUploadField = ( {
	label,
	value,
	onChange,
	width = 60,
	height = 60,
	objectFit = 'contain',
	imageStyle = {},
} ) => {
	const imageUrl = useSelect(
		( select ) => {
			if ( ! value ) {
				return '';
			}
			const image = select( 'core' ).getMedia( value );
			return image?.source_url || '';
		},
		[ value ]
	);

	const altText = label + ' ' + __( 'preview', 'am-portfolio-theme' );

	return (
		<BaseControl
			id={ `media-upload-${ value || 'new' }` }
			label={ label }
			__nextHasNoMarginBottom
		>
			<MediaUploadCheck>
				<MediaUpload
					onSelect={ ( media ) => onChange( media.id ) }
					allowedTypes={ [ 'image' ] }
					value={ value }
					render={ ( { open } ) => (
						<div>
							{ value ? (
								<div>
									<img
										src={ imageUrl }
										alt={ altText }
										style={ {
											display: 'block',
											marginBottom: '8px',
											width: `${ width }px`,
											height: `${ height }px`,
											objectFit,
											...imageStyle,
										} }
										width={ width }
										height={ height }
									/>
									<div
										style={ {
											display: 'flex',
											gap: '8px',
										} }
									>
										<Button
											variant="secondary"
											onClick={ open }
										>
											{ __(
												'Change Image',
												'am-portfolio-theme'
											) }
										</Button>
										<Button
											variant="tertiary"
											onClick={ () => onChange( '' ) }
										>
											{ __(
												'Remove',
												'am-portfolio-theme'
											) }
										</Button>
									</div>
								</div>
							) : (
								<Button variant="secondary" onClick={ open }>
									{ __(
										'Select Image',
										'am-portfolio-theme'
									) }
								</Button>
							) }
						</div>
					) }
				/>
			</MediaUploadCheck>
		</BaseControl>
	);
};

export default MediaUploadField;
