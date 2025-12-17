import { __ } from '@wordpress/i18n';
import { TextControl } from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';
import RichTextControl from '../../js/shared/edit/components/rich-text-control';

export default function Edit( { attributes, setAttributes } ) {
	const { title = '', description = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Projects Hero', 'portfolio' ) }>
			<TextControl
				id="projects-hero-title"
				value={ title }
				label={ __( 'Title', 'portfolio' ) }
				onChange={ ( value ) => updateAttribute( 'title', value ) }
				placeholder={ __( 'Enter hero title', 'portfolio' ) }
			/>

			<RichTextControl
				id="projects-hero-description"
				label={ __( 'Description', 'portfolio' ) }
				value={ description }
				onChange={ ( value ) =>
					updateAttribute( 'description', value )
				}
				placeholder={ __( 'Enter hero description', 'portfolio' ) }
			/>
		</BlockCard>
	);
}
