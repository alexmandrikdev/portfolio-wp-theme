import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Card,
	CardBody,
	CardHeader,
	TextControl,
	ComboboxControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import './editor.scss';
import BlockContainer from '../../js/shared/edit/components/block-container';

export default function Edit( { attributes, setAttributes } ) {
	const {
		title = '',
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
		<BlockContainer>
			<Card style={ { width: '100%' } }>
				<CardHeader>
					<h4>{ __( 'Button Configuration', 'portfolio' ) }</h4>
				</CardHeader>
				<CardBody>
					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Title', 'portfolio' ) }
						id="block-title"
					>
						<TextControl
							id="block-title"
							value={ title }
							onChange={ ( value ) =>
								updateAttribute( 'title', value )
							}
							placeholder={ __( 'Enter title', 'portfolio' ) }
						/>
					</BaseControl>

					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Primary Button Text', 'portfolio' ) }
						id="primary-button-text"
					>
						<TextControl
							id="primary-button-text"
							value={ primaryButtonText }
							onChange={ ( value ) =>
								updateAttribute( 'primary_button_text', value )
							}
							placeholder={ __(
								'Enter primary button text',
								'portfolio'
							) }
						/>
					</BaseControl>

					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Secondary Button Text', 'portfolio' ) }
						id="secondary-button-text"
					>
						<TextControl
							id="secondary-button-text"
							value={ secondaryButtonText }
							onChange={ ( value ) =>
								updateAttribute(
									'secondary_button_text',
									value
								)
							}
							placeholder={ __(
								'Enter secondary button text',
								'portfolio'
							) }
						/>
					</BaseControl>

					<ComboboxControl
						label={ __( 'Secondary Button Page', 'portfolio' ) }
						value={ secondaryButtonPageId }
						onChange={ ( value ) => {
							updateAttribute(
								'secondary_button_page_id',
								value
							);
						} }
						options={ pageOptions }
						allowReset={ true }
					/>
				</CardBody>
			</Card>
		</BlockContainer>
	);
}
