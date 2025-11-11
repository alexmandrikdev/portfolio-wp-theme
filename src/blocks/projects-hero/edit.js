import { __ } from '@wordpress/i18n';
import { BaseControl, TextControl } from '@wordpress/components';
import { RichText } from '@wordpress/block-editor';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const { title = '', description = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Projects Hero', 'portfolio' ) }>
			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Title', 'portfolio' ) }
				id="projects-hero-title"
			>
				<TextControl
					id="projects-hero-title"
					value={ title }
					onChange={ ( value ) => updateAttribute( 'title', value ) }
					placeholder={ __( 'Enter hero title', 'portfolio' ) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Description', 'portfolio' ) }
				id="projects-hero-description"
			>
				<RichText
					tagName="p"
					value={ description }
					onChange={ ( value ) =>
						updateAttribute( 'description', value )
					}
					placeholder={ __( 'Enter hero description', 'portfolio' ) }
					allowedFormats={ [
						'core/bold',
						'core/italic',
						'core/link',
						'core/strikethrough',
					] }
				/>
			</BaseControl>
		</BlockCard>
	);
}
