import { __ } from '@wordpress/i18n';
import { BaseControl, Flex, TextControl } from '@wordpress/components';
import { RichText } from '@wordpress/block-editor';
import BlockCard from '../../js/shared/edit/components/block-card';

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

				<BaseControl
					id="about-story-content"
					__nextHasNoMarginBottom
					label={ __( 'Story Content', 'am-portfolio-theme' ) }
				>
					<RichText
						style={ {
							border: '1px solid #949494',
							borderRadius: '2px',
							padding: '6px 8px',
						} }
						tagName="div"
						value={ content }
						onChange={ ( value ) =>
							updateAttribute( 'content', value )
						}
						placeholder={ __(
							'Tell your story here…',
							'am-portfolio-theme'
						) }
						allowedFormats={ [
							'core/bold',
							'core/italic',
							'core/link',
							'core/strikethrough',
						] }
					/>
				</BaseControl>
			</Flex>
		</BlockCard>
	);
}
