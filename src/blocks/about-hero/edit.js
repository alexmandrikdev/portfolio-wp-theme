import { __ } from '@wordpress/i18n';
import { Flex, TextControl, TextareaControl } from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';
import MediaUploadField from '../../js/shared/edit/components/media-upload-field';

export default function Edit( { attributes, setAttributes } ) {
	const {
		heading = '',
		description = '',
		button_text: buttonText = '',
		profile_image: profileImage = 0,
	} = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'About Hero Section', 'am-portfolio-theme' ) }>
			<Flex direction="column" gap={ 4 }>
				<TextControl
					label={ __( 'Heading', 'am-portfolio-theme' ) }
					value={ heading }
					onChange={ ( value ) =>
						updateAttribute( 'heading', value )
					}
					placeholder={ __(
						'e.g., Hi, I am David',
						'am-portfolio-theme'
					) }
				/>

				<TextareaControl
					label={ __( 'Description', 'am-portfolio-theme' ) }
					value={ description }
					onChange={ ( value ) =>
						updateAttribute( 'description', value )
					}
					placeholder={ __(
						'Brief description about yourself…',
						'am-portfolio-theme'
					) }
					rows={ 4 }
				/>

				<TextControl
					label={ __( 'Button Text', 'am-portfolio-theme' ) }
					value={ buttonText }
					onChange={ ( value ) =>
						updateAttribute( 'button_text', value )
					}
					placeholder={ __(
						"e.g., Let's talk about your project",
						'am-portfolio-theme'
					) }
				/>

				<MediaUploadField
					label={ __( 'Profile Image', 'am-portfolio-theme' ) }
					value={ profileImage }
					onChange={ ( value ) =>
						updateAttribute( 'profile_image', value )
					}
					imageStyle={ {
						borderRadius: '50%',
					} }
				/>
			</Flex>
		</BlockCard>
	);
}
