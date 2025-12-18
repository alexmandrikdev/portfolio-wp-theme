import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	Card,
	CardBody,
	CardHeader,
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

const HobbyItem = ( { item, index, items, onUpdate, onRemove, onMove } ) => {
	const isFirst = index === 0;
	const isLast = index === items.length - 1;

	return (
		<div className="hobby-item">
			<Card style={ { width: '100%', marginBottom: '16px' } }>
				<CardHeader>
					<Flex align="center" gap={ 3 }>
						<MoveButtons
							index={ index }
							isFirst={ isFirst }
							isLast={ isLast }
							onMove={ onMove }
						/>
						<h4>
							{ __( 'Hobby Item', 'am-portfolio-theme' ) } #
							{ index + 1 }
						</h4>
						<RemoveButton
							index={ index }
							onRemove={ onRemove }
							style={ { marginLeft: 'auto' } }
						/>
					</Flex>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 4 }>
						<MediaUploadField
							label={ __( 'Hobby Icon', 'am-portfolio-theme' ) }
							value={ item.icon }
							onChange={ ( value ) =>
								onUpdate( index, 'icon', value )
							}
						/>
						<TextControl
							__nextHasNoMarginBottom
							label={ __( 'Hobby Title', 'am-portfolio-theme' ) }
							value={ item.title }
							onChange={ ( value ) =>
								onUpdate( index, 'title', value )
							}
							placeholder={ __(
								'e.g., Strategic Board Games',
								'am-portfolio-theme'
							) }
						/>
						<TextareaControl
							__nextHasNoMarginBottom
							label={ __(
								'Hobby Description',
								'am-portfolio-theme'
							) }
							value={ item.description }
							onChange={ ( value ) =>
								onUpdate( index, 'description', value )
							}
							placeholder={ __(
								'Describe this hobby…',
								'am-portfolio-theme'
							) }
							rows={ 4 }
						/>
					</Flex>
				</CardBody>
			</Card>
		</div>
	);
};

const HobbiesEditor = ( { items, setAttributes } ) => {
	const { addItem, moveItem, removeItem, updateItem } = useListManagement(
		items,
		setAttributes,
		'hobbies'
	);

	const defaultHobbyItem = {
		icon: 0,
		title: '',
		description: '',
	};

	const canAddMore = items.length < 3;

	return (
		<>
			<BaseControl
				id="about-hobbies-items"
				__nextHasNoMarginBottom
				label={ __( 'Hobby Items', 'am-portfolio-theme' ) }
				help={ __(
					'Add up to 3 hobbies with icons, titles, and descriptions.',
					'am-portfolio-theme'
				) }
			>
				{ items.length > 0 && (
					<div className="hobbies-list">
						{ items.map( ( item, index ) => (
							<HobbyItem
								key={ index }
								item={ item }
								index={ index }
								items={ items }
								onUpdate={ updateItem }
								onRemove={ removeItem }
								onMove={ moveItem }
							/>
						) ) }
					</div>
				) }

				{ canAddMore && (
					<Button
						variant="primary"
						onClick={ () => addItem( defaultHobbyItem ) }
						style={ { marginTop: '16px' } }
					>
						{ __( 'Add Hobby', 'am-portfolio-theme' ) }
					</Button>
				) }

				{ ! canAddMore && (
					<p
						style={ {
							marginTop: '16px',
							color: '#757575',
							fontStyle: 'italic',
						} }
					>
						{ __(
							'Maximum of 3 hobbies reached.',
							'am-portfolio-theme'
						) }
					</p>
				) }
			</BaseControl>
		</>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const { heading = '', hobbies = [] } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard
			title={ __( 'About Hobbies Section', 'am-portfolio-theme' ) }
		>
			<Flex direction="column" gap={ 4 }>
				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Section Heading', 'am-portfolio-theme' ) }
					value={ heading }
					onChange={ ( value ) =>
						updateAttribute( 'heading', value )
					}
					placeholder={ __(
						'e.g., When I am not creating with code…',
						'am-portfolio-theme'
					) }
				/>

				<HobbiesEditor
					items={ hobbies }
					setAttributes={ setAttributes }
				/>
			</Flex>
		</BlockCard>
	);
}
