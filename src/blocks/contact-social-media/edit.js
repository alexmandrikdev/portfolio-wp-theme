import { __ } from '@wordpress/i18n';
import { TextControl, Flex } from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const { title = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Social Media Links', 'am-portfolio-theme' ) }>
			<Flex direction="column" gap={ 4 }>
				<TextControl
					id="contact-social-media-title"
					value={ title }
					label={ __( 'Title', 'am-portfolio-theme' ) }
					onChange={ ( value ) => updateAttribute( 'title', value ) }
					placeholder={ __(
						'Enter title for social media section',
						'am-portfolio-theme'
					) }
					__nextHasNoMarginBottom
				/>
			</Flex>
		</BlockCard>
	);
}
