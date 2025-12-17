import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	Flex,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import './editor.scss';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockCard from '../../js/shared/edit/components/block-card';
import MediaUploadField from '../../js/shared/edit/components/media-upload-field';

const TestimonialItem = ( {
	item,
	index,
	testimonials,
	onUpdate,
	onRemove,
	onMove,
} ) => {
	const isFirst = index === 0;
	const isLast = index === testimonials.length - 1;

	return (
		<div className="testimonial-item">
			<Flex
				align="flex-start"
				gap={ 3 }
				style={ { marginBottom: '16px' } }
			>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
					style={ { alignSelf: 'flex-start', marginTop: '24px' } }
				/>

				<Flex direction="column" gap={ 3 } style={ { flex: 1 } }>
					<TextareaControl
						label={ __( 'Quote', 'am-portfolio-theme' ) }
						value={ item.quote || '' }
						onChange={ ( value ) =>
							onUpdate( index, 'quote', value )
						}
						placeholder={ __(
							'Enter the testimonial quote…',
							'am-portfolio-theme'
						) }
					/>

					<TextControl
						label={ __( 'Author', 'am-portfolio-theme' ) }
						value={ item.author || '' }
						onChange={ ( value ) =>
							onUpdate( index, 'author', value )
						}
						placeholder={ __(
							'Enter author name…',
							'am-portfolio-theme'
						) }
					/>

					<TextControl
						label={ __( 'Role', 'am-portfolio-theme' ) }
						value={ item.role || '' }
						onChange={ ( value ) =>
							onUpdate( index, 'role', value )
						}
						placeholder={ __(
							'Enter author role/position…',
							'am-portfolio-theme'
						) }
					/>

					<TextControl
						label={ __(
							'Social Media Link',
							'am-portfolio-theme'
						) }
						value={ item.social_media_link || '' }
						onChange={ ( value ) =>
							onUpdate( index, 'social_media_link', value )
						}
						placeholder={ __(
							'https://linkedin.com/in/username',
							'am-portfolio-theme'
						) }
					/>
				</Flex>

				<MediaUploadField
					label={ __( 'Profile Picture', 'am-portfolio-theme' ) }
					value={ item.profile_picture || '' }
					onChange={ ( value ) =>
						onUpdate( index, 'profile_picture', value )
					}
					width={ 64 }
					height={ 64 }
					objectFit="cover"
					imageStyle={ { borderRadius: '50%' } }
				/>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-start', marginTop: '24px' } }
				/>
			</Flex>

			{ ! isLast && <hr className="testimonial-item-separator" /> }
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const { testimonials = [] } = attributes;
	const { addItem, moveItem, removeItem, updateItem } = useListManagement(
		testimonials,
		setAttributes,
		'testimonials'
	);

	const defaultTestimonialItem = {
		quote: '',
		author: '',
		role: '',
		profile_picture: '',
		social_media_link: '',
	};

	return (
		<BlockCard title={ __( 'Project Testimonials', 'am-portfolio-theme' ) }>
			<BaseControl
				id="project-testimonial-testimonials"
				__nextHasNoMarginBottom
				label={ __( 'Testimonials', 'am-portfolio-theme' ) }
				help={ __(
					'Add testimonials from clients or team members about the project.',
					'am-portfolio-theme'
				) }
			>
				{ testimonials.length > 0 && (
					<div className="testimonials-list">
						{ testimonials.map( ( item, index ) => (
							<TestimonialItem
								key={ index }
								item={ item }
								index={ index }
								testimonials={ testimonials }
								onUpdate={ updateItem }
								onRemove={ removeItem }
								onMove={ moveItem }
							/>
						) ) }
					</div>
				) }

				<Button
					variant="primary"
					onClick={ () => addItem( defaultTestimonialItem ) }
					style={ { marginTop: '16px' } }
				>
					{ __( 'Add Testimonial', 'am-portfolio-theme' ) }
				</Button>
			</BaseControl>
		</BlockCard>
	);
}
