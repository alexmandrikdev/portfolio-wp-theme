import { __ } from '@wordpress/i18n';
import { BaseControl, TextControl } from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const { title = '', description = '', cta_text: ctaText = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Contact Hero Content', 'portfolio' ) }>
			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Title', 'portfolio' ) }
				id="contact-hero-title"
			>
				<TextControl
					id="contact-hero-title"
					value={ title }
					onChange={ ( value ) => updateAttribute( 'title', value ) }
					placeholder={ __( 'Enter title', 'portfolio' ) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Description', 'portfolio' ) }
				id="contact-hero-description"
			>
				<TextControl
					id="contact-hero-description"
					value={ description }
					onChange={ ( value ) =>
						updateAttribute( 'description', value )
					}
					placeholder={ __( 'Enter description', 'portfolio' ) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'CTA Button Text', 'portfolio' ) }
				id="contact-hero-cta-text"
			>
				<TextControl
					id="contact-hero-cta-text"
					value={ ctaText }
					onChange={ ( value ) =>
						updateAttribute( 'cta_text', value )
					}
					placeholder={ __( 'Enter CTA button text', 'portfolio' ) }
				/>
			</BaseControl>
		</BlockCard>
	);
}
