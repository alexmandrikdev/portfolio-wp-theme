import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	Flex,
	FlexBlock,
	TextControl,
} from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import './editor.scss';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockCard from '../../js/shared/edit/components/block-card';

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
											alt={ __(
												'Screenshot preview',
												'am-portfolio-theme'
											) }
											style={ {
												maxWidth: '150px',
												maxHeight: '100px',
												height: 'auto',
												display: 'block',
												marginBottom: '8px',
											} }
										/>
										<Button
											variant="secondary"
											onClick={ open }
										>
											{ __(
												'Replace Image',
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

const ScreenshotItem = ( {
	item,
	index,
	screenshots,
	onUpdate,
	onRemove,
	onMove,
} ) => {
	const isFirst = index === 0;
	const isLast = index === screenshots.length - 1;

	return (
		<div className="screenshot-item">
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

				<TextInput
					label={ __( 'Page/Section Title', 'am-portfolio-theme' ) }
					value={ item.title || '' }
					onChange={ ( value ) => onUpdate( index, 'title', value ) }
					placeholder={ __( 'e.g., Homepage', 'am-portfolio-theme' ) }
				/>

				<MediaUploadField
					label={ __(
						'Desktop Screenshot (1568×1017)',
						'am-portfolio-theme'
					) }
					value={ item.desktop_screenshot_id || '' }
					onChange={ ( value ) =>
						onUpdate( index, 'desktop_screenshot_id', value )
					}
				/>

				<MediaUploadField
					label={ __(
						'Mobile Screenshot (440×866)',
						'am-portfolio-theme'
					) }
					value={ item.mobile_screenshot_id || '' }
					onChange={ ( value ) =>
						onUpdate( index, 'mobile_screenshot_id', value )
					}
				/>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-start', marginTop: '24px' } }
				/>
			</Flex>

			{ ! isLast && <hr className="screenshot-item-separator" /> }
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const { screenshots = [] } = attributes;
	const { addItem, moveItem, removeItem, updateItem } = useListManagement(
		screenshots,
		setAttributes,
		'screenshots'
	);

	const defaultScreenshotItem = {
		title: '',
		desktop_screenshot_id: '',
		mobile_screenshot_id: '',
	};

	return (
		<BlockCard
			title={ __(
				'Project Results - Screenshots',
				'am-portfolio-theme'
			) }
		>
			<BaseControl
				id="project-results-screenshots"
				__nextHasNoMarginBottom
				label={ __( 'Additional Screenshots', 'am-portfolio-theme' ) }
				help={ __(
					'Add paired desktop and mobile screenshots for different pages or sections of the project.',
					'am-portfolio-theme'
				) }
			>
				{ screenshots.length > 0 && (
					<div className="screenshots-list">
						{ screenshots.map( ( item, index ) => (
							<ScreenshotItem
								key={ index }
								item={ item }
								index={ index }
								screenshots={ screenshots }
								onUpdate={ updateItem }
								onRemove={ removeItem }
								onMove={ moveItem }
							/>
						) ) }
					</div>
				) }

				<Button
					variant="primary"
					onClick={ () => addItem( defaultScreenshotItem ) }
					style={ { marginTop: '16px' } }
				>
					{ __( 'Add Screenshot', 'am-portfolio-theme' ) }
				</Button>
			</BaseControl>
		</BlockCard>
	);
}
