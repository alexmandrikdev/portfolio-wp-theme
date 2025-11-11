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
												'Value icon preview',
												'am-portfolio-theme'
											) }
											style={ {
												display: 'block',
												marginBottom: '8px',
												width: '60px',
												height: '60px',
												objectFit: 'contain',
												borderRadius:
													'var(--radius-md)',
											} }
										/>
										<Button
											variant="secondary"
											onClick={ open }
										>
											{ __(
												'Replace Icon',
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
											'Select Icon',
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

const TextareaInput = ( { label, value, onChange, placeholder } ) => (
	<FlexBlock>
		<TextareaControl
			label={ label }
			value={ value }
			onChange={ onChange }
			placeholder={ placeholder }
			rows={ 4 }
		/>
	</FlexBlock>
);

const ValueCardItem = ( {
	item,
	index,
	valueCards,
	onUpdate,
	onRemove,
	onMove,
} ) => {
	const isFirst = index === 0;
	const isLast = index === valueCards.length - 1;

	return (
		<div className="value-card-item">
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
					<TextInput
						label={ __( 'Title', 'am-portfolio-theme' ) }
						value={ item.title || '' }
						onChange={ ( value ) =>
							onUpdate( index, 'title', value )
						}
						placeholder={ __(
							'e.g., Performance is Priority #1',
							'am-portfolio-theme'
						) }
					/>

					<TextareaInput
						label={ __( 'Description', 'am-portfolio-theme' ) }
						value={ item.description || '' }
						onChange={ ( value ) =>
							onUpdate( index, 'description', value )
						}
						placeholder={ __(
							'Describe the value proposition…',
							'am-portfolio-theme'
						) }
					/>
				</Flex>

				<MediaUploadField
					label={ __( 'Icon', 'am-portfolio-theme' ) }
					value={ item.icon || '' }
					onChange={ ( value ) => onUpdate( index, 'icon', value ) }
				/>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-start', marginTop: '24px' } }
				/>
			</Flex>

			{ ! isLast && <hr className="value-card-item-separator" /> }
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const { title, value_cards: valueCards = [] } = attributes;
	const { addItem, moveItem, removeItem, updateItem } = useListManagement(
		valueCards,
		setAttributes,
		'value_cards'
	);

	const defaultValueCard = {
		icon: '',
		title: '',
		description: '',
	};

	return (
		<BlockCard title={ __( 'Services Values', 'am-portfolio-theme' ) }>
			<TextInput
				label={ __( 'Section Title', 'am-portfolio-theme' ) }
				value={ title || '' }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				placeholder={ __(
					'What I Provide in Every Project',
					'am-portfolio-theme'
				) }
			/>

			<BaseControl
				id="services-values-cards"
				__nextHasNoMarginBottom
				label={ __( 'Value Cards', 'am-portfolio-theme' ) }
				help={ __(
					'Add value propositions that you provide in every project.',
					'am-portfolio-theme'
				) }
			>
				{ valueCards.length > 0 && (
					<div className="value-cards-list">
						{ valueCards.map( ( item, index ) => (
							<ValueCardItem
								key={ index }
								item={ item }
								index={ index }
								valueCards={ valueCards }
								onUpdate={ updateItem }
								onRemove={ removeItem }
								onMove={ moveItem }
							/>
						) ) }
					</div>
				) }

				<Button
					variant="primary"
					onClick={ () => addItem( defaultValueCard ) }
					style={ { marginTop: '16px' } }
				>
					{ __( 'Add Value Card', 'am-portfolio-theme' ) }
				</Button>
			</BaseControl>
		</BlockCard>
	);
}
