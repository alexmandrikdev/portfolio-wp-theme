import { __ } from '@wordpress/i18n';
import { TextControl, Flex } from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const { title = '', description = '', cta_text: ctaText = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Contact Hero Content', 'portfolio' ) }>
			<Flex direction="column" gap={ 4 }>
				<TextControl
					id="contact-hero-title"
					value={ title }
					label={ __( 'Title', 'portfolio' ) }
					onChange={ ( value ) => updateAttribute( 'title', value ) }
					placeholder={ __( 'Enter title', 'portfolio' ) }
					__nextHasNoMarginBottom
				/>

				<TextControl
					id="contact-hero-description"
					value={ description }
					label={ __( 'Description', 'portfolio' ) }
					onChange={ ( value ) =>
						updateAttribute( 'description', value )
					}
					placeholder={ __( 'Enter description', 'portfolio' ) }
					__nextHasNoMarginBottom
				/>

				<TextControl
					id="contact-hero-cta-text"
					value={ ctaText }
					label={ __( 'CTA Button Text', 'portfolio' ) }
					onChange={ ( value ) =>
						updateAttribute( 'cta_text', value )
					}
					placeholder={ __( 'Enter CTA button text', 'portfolio' ) }
					__nextHasNoMarginBottom
				/>
			</Flex>
		</BlockCard>
	);
}
