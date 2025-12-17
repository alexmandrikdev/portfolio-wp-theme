import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Flex,
	TextControl,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components';
import { RichText } from '@wordpress/block-editor';
import MediaUploadField from '../../../js/shared/edit/components/media-upload-field';

const ALLOWED_FORMATS = [
	'core/bold',
	'core/italic',
	'core/link',
	'core/strikethrough',
];

const PackageFields = ( { packageData, index, updatePackageField } ) => {
	return (
		<Flex direction="column" gap={ 4 }>
			<ToggleControl
				label={ __( 'Featured Package', 'am-portfolio-theme' ) }
				checked={ packageData.is_featured || false }
				onChange={ ( value ) =>
					updatePackageField( 'is_featured', value )
				}
			/>

			{ packageData.is_featured && (
				<TextControl
					label={ __( 'Featured Label', 'am-portfolio-theme' ) }
					value={ packageData.featured_label }
					onChange={ ( value ) =>
						updatePackageField( 'featured_label', value )
					}
					placeholder={ __( 'Most Popular', 'am-portfolio-theme' ) }
				/>
			) }

			<MediaUploadField
				label={ __( 'Package Icon', 'am-portfolio-theme' ) }
				value={ packageData.icon || '' }
				onChange={ ( value ) => updatePackageField( 'icon', value ) }
			/>

			<TextControl
				label={ __( 'Package Title', 'am-portfolio-theme' ) }
				value={ packageData.title || '' }
				onChange={ ( value ) => updatePackageField( 'title', value ) }
				placeholder={ __(
					'e.g., Complete Development',
					'am-portfolio-theme'
				) }
			/>

			<TextareaControl
				label={ __( 'Package Description', 'am-portfolio-theme' ) }
				value={ packageData.description || '' }
				onChange={ ( value ) =>
					updatePackageField( 'description', value )
				}
				placeholder={ __(
					'Brief description of the package…',
					'am-portfolio-theme'
				) }
			/>

			<TextControl
				label={ __( 'Highlighted Value Title', 'am-portfolio-theme' ) }
				value={ packageData.highlighted_value_title || '' }
				onChange={ ( value ) =>
					updatePackageField( 'highlighted_value_title', value )
				}
				placeholder={ __( 'Highlighted Value', 'am-portfolio-theme' ) }
			/>

			<BaseControl
				id={ `package-${ index }-highlighted-value` }
				__nextHasNoMarginBottom
				label={ __(
					'Highlighted Value Content',
					'am-portfolio-theme'
				) }
			>
				<RichText
					style={ {
						border: '1px solid #949494',
						borderRadius: '2px',
						padding: '6px 8px',
					} }
					tagName="p"
					value={ packageData.highlighted_value || '' }
					onChange={ ( value ) =>
						updatePackageField( 'highlighted_value', value )
					}
					placeholder={ __(
						'What makes this package special…',
						'am-portfolio-theme'
					) }
					allowedFormats={ ALLOWED_FORMATS }
				/>
			</BaseControl>

			<TextControl
				label={ __( 'Design Approach Title', 'am-portfolio-theme' ) }
				value={ packageData.design_approach_title || '' }
				onChange={ ( value ) =>
					updatePackageField( 'design_approach_title', value )
				}
				placeholder={ __( 'Design Approach', 'am-portfolio-theme' ) }
			/>

			<BaseControl
				id={ `package-${ index }-design-approach` }
				__nextHasNoMarginBottom
				label={ __( 'Design Approach Content', 'am-portfolio-theme' ) }
			>
				<RichText
					style={ {
						border: '1px solid #949494',
						borderRadius: '2px',
						padding: '6px 8px',
					} }
					tagName="p"
					value={ packageData.design_approach || '' }
					onChange={ ( value ) =>
						updatePackageField( 'design_approach', value )
					}
					placeholder={ __(
						'How we approach design for this package…',
						'am-portfolio-theme'
					) }
					allowedFormats={ ALLOWED_FORMATS }
				/>
			</BaseControl>

			<TextControl
				label={ __( 'Button Text', 'am-portfolio-theme' ) }
				value={ packageData.button_text || '' }
				onChange={ ( value ) =>
					updatePackageField( 'button_text', value )
				}
				placeholder={ __( 'e.g., Get Started', 'am-portfolio-theme' ) }
			/>
		</Flex>
	);
};

export default PackageFields;
