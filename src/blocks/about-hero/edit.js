import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	Flex,
	FlexBlock,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import BlockCard from '../../js/shared/edit/components/block-card';

const TextInput = ( { label, value, onChange, placeholder } ) => (
	<FlexBlock>
		<TextControl
			label={ label }
			value={ value }
			onChange={ onChange }
			placeholder={ placeholder }
		/>
	</FlexBlock>
);

const TextareaInput = ( { label, value, onChange, placeholder } ) => (
	<FlexBlock>
		<TextareaControl
			label={ label }
			value={ value }
			onChange={ onChange }
			placeholder={ placeholder }
		/>
	</FlexBlock>
);

const MediaUploadField = ( { label, value, onChange } ) => {
	const imageUrl = useSelect(
		( select ) => {
			if ( ! value ) {
				return '';
			}
			const image = select( 'core' ).getMedia( value );
			return image?.source_url || '';
		},
		[ value ]
	);

	return (
		<FlexBlock>
			<BaseControl
				id={ `media-upload-${ value || 'new' }` }
				label={ label }
			>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) => onChange( media.id ) }
						allowedTypes={ [ 'image' ] }
						value={ value }
						render={ ( { open } ) => (
							<div>
								{ value ? (
									<div>
										<img
											src={ imageUrl }
											alt=""
											style={ {
												display: 'block',
												marginBottom: '8px',
												width: '60px',
												height: '60px',
												objectFit: 'contain',
											} }
										/>
										<Button
											variant="secondary"
											onClick={ open }
										>
											{ __(
												'Change Image',
												'am-portfolio-theme'
											) }
										</Button>
									</div>
								) : (
									<Button
										variant="secondary"
										onClick={ open }
									>
										{ __(
											'Select Image',
											'am-portfolio-theme'
										) }
									</Button>
								) }
							</div>
						) }
					/>
				</MediaUploadCheck>
			</BaseControl>
		</FlexBlock>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const {
		heading = '',
		description = '',
		button_text: buttonText = '',
		profile_image: profileImage = 0,
	} = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'About Hero Section', 'am-portfolio-theme' ) }>
			<Flex direction="column" gap={ 4 }>
				<TextInput
					label={ __( 'Heading', 'am-portfolio-theme' ) }
					value={ heading }
					onChange={ ( value ) =>
						updateAttribute( 'heading', value )
					}
					placeholder={ __(
						'e.g., Hi, I am David',
						'am-portfolio-theme'
					) }
				/>

				<TextareaInput
					label={ __( 'Description', 'am-portfolio-theme' ) }
					value={ description }
					onChange={ ( value ) =>
						updateAttribute( 'description', value )
					}
					placeholder={ __(
						'Brief description about yourself…',
						'am-portfolio-theme'
					) }
				/>

				<TextInput
					label={ __( 'Button Text', 'am-portfolio-theme' ) }
					value={ buttonText }
					onChange={ ( value ) =>
						updateAttribute( 'button_text', value )
					}
					placeholder={ __(
						"e.g., Let's talk about your project",
						'am-portfolio-theme'
					) }
				/>

				<MediaUploadField
					label={ __( 'Profile Image', 'am-portfolio-theme' ) }
					value={ profileImage }
					onChange={ ( value ) =>
						updateAttribute( 'profile_image', value )
					}
				/>
			</Flex>
		</BlockCard>
	);
}
