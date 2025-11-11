import { __ } from '@wordpress/i18n';
import { BaseControl, TextControl } from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const { title = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Social Media Links', 'am-portfolio-theme' ) }>
			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Title', 'am-portfolio-theme' ) }
				id="contact-social-media-title"
			>
				<TextControl
					id="contact-social-media-title"
					value={ title }
					onChange={ ( value ) => updateAttribute( 'title', value ) }
					placeholder={ __(
						'Enter title for social media section',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>
		</BlockCard>
	);
}
