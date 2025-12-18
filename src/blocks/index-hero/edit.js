import { __ } from '@wordpress/i18n';
import { TextControl, Flex } from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const { title = '', subtitle = '', cta_note: ctaNote = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Hero Content', 'portfolio' ) }>
			<Flex direction="column" gap={ 4 }>
				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Title', 'portfolio' ) }
					id="hero-title"
					value={ title }
					onChange={ ( value ) => updateAttribute( 'title', value ) }
					placeholder={ __( 'Enter title', 'portfolio' ) }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Subtitle', 'portfolio' ) }
					id="hero-subtitle"
					value={ subtitle }
					onChange={ ( value ) =>
						updateAttribute( 'subtitle', value )
					}
					placeholder={ __( 'Enter subtitle', 'portfolio' ) }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Call to Action Note', 'portfolio' ) }
					id="hero-cta-note"
					value={ ctaNote }
					onChange={ ( value ) =>
						updateAttribute( 'cta_note', value )
					}
					placeholder={ __( 'Enter CTA note', 'portfolio' ) }
				/>
			</Flex>
		</BlockCard>
	);
}
