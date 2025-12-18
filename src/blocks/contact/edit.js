import { __ } from '@wordpress/i18n';
import {
	TextControl,
	TextareaControl,
	ComboboxControl,
	Flex,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const {
		title = '',
		subtitle = '',
		primary_button_text: primaryButtonText = '',
		secondary_button_text: secondaryButtonText = '',
		secondary_button_page_id: secondaryButtonPageId = null,
	} = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	const pageOptions = useSelect( ( select ) => {
		const { getEntityRecords } = select( 'core' );
		const records = getEntityRecords( 'postType', 'page', {
			per_page: -1,
			status: 'publish',
		} );

		if ( ! records ) {
			return [];
		}

		return records.map( ( page ) => ( {
			label: page.title.rendered,
			value: page.id,
		} ) );
	}, [] );

	return (
		<BlockCard title={ __( 'Contact Section', 'portfolio' ) }>
			<Flex direction="column" gap={ 4 }>
				<TextControl
					id="block-title"
					value={ title }
					label={ __( 'Title', 'portfolio' ) }
					onChange={ ( value ) => updateAttribute( 'title', value ) }
					placeholder={ __( 'Enter title', 'portfolio' ) }
					__nextHasNoMarginBottom
				/>

				<TextareaControl
					id="block-subtitle"
					value={ subtitle }
					label={ __( 'Subtitle', 'portfolio' ) }
					onChange={ ( value ) =>
						updateAttribute( 'subtitle', value )
					}
					placeholder={ __( 'Enter subtitle', 'portfolio' ) }
					rows={ 3 }
					__nextHasNoMarginBottom
				/>

				<TextControl
					id="primary-button-text"
					value={ primaryButtonText }
					label={ __( 'Primary Button Text', 'portfolio' ) }
					onChange={ ( value ) =>
						updateAttribute( 'primary_button_text', value )
					}
					placeholder={ __(
						'Enter primary button text',
						'portfolio'
					) }
					__nextHasNoMarginBottom
				/>

				<TextControl
					id="secondary-button-text"
					value={ secondaryButtonText }
					label={ __( 'Secondary Button Text', 'portfolio' ) }
					onChange={ ( value ) =>
						updateAttribute( 'secondary_button_text', value )
					}
					placeholder={ __(
						'Enter secondary button text',
						'portfolio'
					) }
					__nextHasNoMarginBottom
				/>

				<ComboboxControl
					label={ __( 'Secondary Button Page', 'portfolio' ) }
					value={ secondaryButtonPageId }
					onChange={ ( value ) => {
						updateAttribute( 'secondary_button_page_id', value );
					} }
					options={ pageOptions }
					allowReset={ true }
				/>
			</Flex>
		</BlockCard>
	);
}
