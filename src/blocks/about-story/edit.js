import { __ } from '@wordpress/i18n';
import { Flex, TextControl } from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';
import RichTextControl from '../../js/shared/edit/components/rich-text-control';

export default function Edit( { attributes, setAttributes } ) {
	const { heading = '', content = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'About Story Section', 'am-portfolio-theme' ) }>
			<Flex direction="column" gap={ 4 }>
				<TextControl
					label={ __( 'Heading', 'am-portfolio-theme' ) }
					value={ heading }
					onChange={ ( value ) =>
						updateAttribute( 'heading', value )
					}
					placeholder={ __(
						'e.g., The Joy of Creation',
						'am-portfolio-theme'
					) }
				/>

				<RichTextControl
					id="about-story-content"
					label={ __( 'Story Content', 'am-portfolio-theme' ) }
					value={ content }
					onChange={ ( value ) =>
						updateAttribute( 'content', value )
					}
					placeholder={ __(
						'Tell your story here…',
						'am-portfolio-theme'
					) }
				/>
			</Flex>
		</BlockCard>
	);
}
