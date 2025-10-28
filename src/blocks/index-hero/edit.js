import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Card,
	CardBody,
	CardHeader,
	TextControl,
} from '@wordpress/components';
import BlockContainer from '../../js/shared/edit/components/block-container';

export default function Edit( { attributes, setAttributes } ) {
	const { title = '', subtitle = '', cta_note: ctaNote = '' } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockContainer>
			<Card style={ { width: '100%' } }>
				<CardHeader>
					<h4>{ __( 'Hero Content', 'portfolio' ) }</h4>
				</CardHeader>
				<CardBody>
					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Title', 'portfolio' ) }
						id="hero-title"
					>
						<TextControl
							id="hero-title"
							value={ title }
							onChange={ ( value ) =>
								updateAttribute( 'title', value )
							}
							placeholder={ __( 'Enter title', 'portfolio' ) }
						/>
					</BaseControl>

					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Subtitle', 'portfolio' ) }
						id="hero-subtitle"
					>
						<TextControl
							id="hero-subtitle"
							value={ subtitle }
							onChange={ ( value ) =>
								updateAttribute( 'subtitle', value )
							}
							placeholder={ __( 'Enter subtitle', 'portfolio' ) }
						/>
					</BaseControl>

					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Call to Action Note', 'portfolio' ) }
						id="hero-cta-note"
					>
						<TextControl
							id="hero-cta-note"
							value={ ctaNote }
							onChange={ ( value ) =>
								updateAttribute( 'cta_note', value )
							}
							placeholder={ __( 'Enter CTA note', 'portfolio' ) }
						/>
					</BaseControl>
				</CardBody>
			</Card>
		</BlockContainer>
	);
}
