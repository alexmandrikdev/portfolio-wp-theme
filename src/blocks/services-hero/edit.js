import { __ } from '@wordpress/i18n';
import { TextControl, TextareaControl, Flex } from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const { title = '', subtitle = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Services Hero', 'portfolio' ) }>
			<Flex direction="column" gap={ 4 }>
				<TextControl
					id="services-hero-title"
					value={ title }
					label={ __( 'Title', 'portfolio' ) }
					onChange={ ( value ) => updateAttribute( 'title', value ) }
					placeholder={ __( 'Enter hero title', 'portfolio' ) }
					__nextHasNoMarginBottom
				/>

				<TextareaControl
					id="services-hero-subtitle"
					value={ subtitle }
					label={ __( 'Subtitle', 'portfolio' ) }
					onChange={ ( value ) =>
						updateAttribute( 'subtitle', value )
					}
					placeholder={ __( 'Enter hero subtitle', 'portfolio' ) }
					rows={ 4 }
					__nextHasNoMarginBottom
				/>
			</Flex>
		</BlockCard>
	);
}
