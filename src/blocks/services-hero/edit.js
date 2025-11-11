import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const { title = '', subtitle = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Services Hero', 'portfolio' ) }>
			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Title', 'portfolio' ) }
				id="services-hero-title"
			>
				<TextControl
					id="services-hero-title"
					value={ title }
					onChange={ ( value ) => updateAttribute( 'title', value ) }
					placeholder={ __( 'Enter hero title', 'portfolio' ) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Subtitle', 'portfolio' ) }
				id="services-hero-subtitle"
			>
				<TextareaControl
					id="services-hero-subtitle"
					value={ subtitle }
					onChange={ ( value ) =>
						updateAttribute( 'subtitle', value )
					}
					placeholder={ __( 'Enter hero subtitle', 'portfolio' ) }
					rows={ 4 }
				/>
			</BaseControl>
		</BlockCard>
	);
}
