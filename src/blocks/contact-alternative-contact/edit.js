import { __ } from '@wordpress/i18n';
import { BaseControl, TextControl } from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const { text = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Alternative Contact Content', 'portfolio' ) }>
			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Contact Text', 'portfolio' ) }
				id="alternative-contact-text"
			>
				<TextControl
					id="alternative-contact-text"
					value={ text }
					onChange={ ( value ) => updateAttribute( 'text', value ) }
					placeholder={ __(
						'Enter contact text (use [email] for email placeholder)',
						'portfolio'
					) }
					help={ __(
						'Use [email] as a placeholder for the contact email address from theme settings.',
						'portfolio'
					) }
				/>
			</BaseControl>
		</BlockCard>
	);
}
