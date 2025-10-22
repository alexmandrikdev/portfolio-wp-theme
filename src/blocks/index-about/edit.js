import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	Card,
	CardBody,
	CardHeader,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import BlockContainer from '../../js/shared/edit/components/block-container';

export default function Edit( { attributes, setAttributes } ) {
	const {
		profile_image: profileImage = '',
		title = '',
		name_highlight: nameHighlight = '',
		description = '',
	} = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	const imageUrl = useSelect(
		( select ) => {
			if ( ! profileImage ) {
				return '';
			}
			const image = select( 'core' ).getMedia( profileImage );
			return image?.source_url || '';
		},
		[ profileImage ]
	);

	return (
		<BlockContainer>
			<Card style={ { width: '100%' } }>
				<CardHeader>
					<h4>{ __( 'About Section', 'portfolio' ) }</h4>
				</CardHeader>
				<CardBody>
					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Profile Image', 'portfolio' ) }
						id="profile-image"
					>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( media ) =>
									updateAttribute( 'profile_image', media.id )
								}
								allowedTypes={ [ 'image' ] }
								value={ profileImage }
								render={ ( { open } ) => (
									<div style={ { marginBottom: '16px' } }>
										{ ! profileImage ? (
											<Button
												variant="secondary"
												onClick={ open }
											>
												{ __(
													'Select Image',
													'portfolio'
												) }
											</Button>
										) : (
											<div>
												<img
													src={ imageUrl }
													alt={ __(
														'Profile image',
														'portfolio'
													) }
													style={ {
														maxWidth: '200px',
														height: 'auto',
														display: 'block',
														marginBottom: '8px',
													} }
												/>
												<div>
													<Button
														variant="secondary"
														onClick={ open }
														style={ {
															marginRight: '8px',
														} }
													>
														{ __(
															'Change Image',
															'portfolio'
														) }
													</Button>
													<Button
														variant="tertiary"
														onClick={ () =>
															updateAttribute(
																'profile_image',
																''
															)
														}
													>
														{ __(
															'Remove',
															'portfolio'
														) }
													</Button>
												</div>
											</div>
										) }
									</div>
								) }
							/>
						</MediaUploadCheck>
					</BaseControl>

					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Title', 'portfolio' ) }
						id="profile-title"
					>
						<TextControl
							id="profile-title"
							value={ title }
							onChange={ ( value ) =>
								updateAttribute( 'title', value )
							}
							placeholder={ __( 'Enter title', 'portfolio' ) }
						/>
					</BaseControl>

					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Name Highlight', 'portfolio' ) }
						id="name-highlight"
					>
						<TextareaControl
							id="name-highlight"
							value={ nameHighlight }
							onChange={ ( value ) =>
								updateAttribute( 'name_highlight', value )
							}
							placeholder={ __(
								'Enter highlighted name text',
								'portfolio'
							) }
							rows={ 3 }
						/>
					</BaseControl>

					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Description', 'portfolio' ) }
						id="description"
					>
						<TextareaControl
							id="description"
							value={ description }
							onChange={ ( value ) =>
								updateAttribute( 'description', value )
							}
							placeholder={ __(
								'Enter description',
								'portfolio'
							) }
							rows={ 5 }
						/>
					</BaseControl>
				</CardBody>
			</Card>
		</BlockContainer>
	);
}
