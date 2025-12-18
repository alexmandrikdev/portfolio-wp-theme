import { __ } from '@wordpress/i18n';
import { Flex, TextControl, TextareaControl } from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';
import MediaUploadField from '../../js/shared/edit/components/media-upload-field';

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

	return (
		<BlockCard title={ __( 'About Section', 'portfolio' ) }>
			<Flex direction="column" gap={ 4 }>
				<MediaUploadField
					label={ __( 'Profile Image', 'portfolio' ) }
					value={ profileImage }
					onChange={ ( value ) =>
						updateAttribute( 'profile_image', value )
					}
					width={ 200 }
					height={ 200 }
					imageStyle={ { borderRadius: '50%' } }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Title', 'portfolio' ) }
					id="profile-title"
					value={ title }
					onChange={ ( value ) => updateAttribute( 'title', value ) }
					placeholder={ __( 'Enter title', 'portfolio' ) }
				/>

				<TextareaControl
					__nextHasNoMarginBottom
					label={ __( 'Name Highlight', 'portfolio' ) }
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

				<TextareaControl
					__nextHasNoMarginBottom
					label={ __( 'Description', 'portfolio' ) }
					id="description"
					value={ description }
					onChange={ ( value ) =>
						updateAttribute( 'description', value )
					}
					placeholder={ __( 'Enter description', 'portfolio' ) }
					rows={ 5 }
				/>
			</Flex>
		</BlockCard>
	);
}
