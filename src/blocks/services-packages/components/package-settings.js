import { __ } from '@wordpress/i18n';
import {
	Card,
	CardBody,
	CardHeader,
	Flex,
	TextControl,
	TextareaControl,
} from '@wordpress/components';

const PackageSettings = ( { title, subtitle, setAttributes } ) => {
	return (
		<Card style={ { width: '100%', marginBottom: '24px' } }>
			<CardHeader>
				<h4>{ __( 'Section Settings', 'am-portfolio-theme' ) }</h4>
			</CardHeader>
			<CardBody>
				<Flex direction="column" gap={ 4 }>
					<TextControl
						label={ __( 'Section Title', 'am-portfolio-theme' ) }
						value={ title }
						onChange={ ( value ) =>
							setAttributes( { title: value } )
						}
						placeholder={ __(
							'e.g., Service Packages',
							'am-portfolio-theme'
						) }
					/>

					<TextareaControl
						label={ __( 'Section Subtitle', 'am-portfolio-theme' ) }
						value={ subtitle }
						onChange={ ( value ) =>
							setAttributes( { subtitle: value } )
						}
						placeholder={ __(
							'Brief description of the packages…',
							'am-portfolio-theme'
						) }
					/>
				</Flex>
			</CardBody>
		</Card>
	);
};

export default PackageSettings;
