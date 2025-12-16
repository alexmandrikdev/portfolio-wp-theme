import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	ComboboxControl,
	Flex,
	FlexBlock,
	TextControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockCard from '../../js/shared/edit/components/block-card';
import './editor.scss';

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

const MenuItem = ( { item, index, items, onUpdate, onRemove, onMove } ) => {
	const isFirst = index === 0;
	const isLast = index === items.length - 1;

	const pages =
		useSelect( ( select ) => {
			return select( coreDataStore ).getEntityRecords(
				'postType',
				'page',
				{
					per_page: -1,
				}
			);
		} ) || [];

	const pageOptions = pages.map( ( page ) => {
		return {
			label: page.title.rendered,
			value: page.id,
		};
	} );

	return (
		<div className="menu-item">
			<Flex align="center" gap={ 3 } style={ { marginBottom: '12px' } }>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
				/>

				<FlexBlock>
					<ComboboxControl
						label={ __( 'Page', 'portfolio' ) }
						value={ item.page_id || '' }
						onChange={ ( value ) =>
							onUpdate( index, 'page_id', value )
						}
						options={ pageOptions }
					/>
				</FlexBlock>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-end', marginBottom: '8px' } }
				/>
			</Flex>
			{ ! isLast && <hr className="menu-item-separator" /> }
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const { menu_items: menuItems = [], cta_text: ctaText } = attributes;
	const { addItem, moveItem, removeItem, updateItem } = useListManagement(
		menuItems,
		setAttributes,
		'menu_items'
	);

	const defaultMenuItem = { page_id: '' };

	return (
		<BlockCard title={ __( 'Header', 'portfolio' ) }>
			<TextInput
				label={ __( 'CTA Text', 'portfolio' ) }
				value={ ctaText }
				onChange={ ( value ) => setAttributes( { cta_text: value } ) }
				help={ __(
					'Text displayed in the call-to-action button',
					'portfolio'
				) }
			/>
			<BaseControl
				id="header-menu-items"
				__nextHasNoMarginBottom
				label={ __( 'Menu Items', 'portfolio' ) }
				help={ __(
					'Add navigation menu items by selecting pages.',
					'portfolio'
				) }
			>
				{ menuItems.length > 0 && (
					<div className="menu-items-list">
						{ menuItems.map( ( item, index ) => (
							<MenuItem
								key={ index }
								item={ item }
								index={ index }
								items={ menuItems }
								onUpdate={ updateItem }
								onRemove={ removeItem }
								onMove={ moveItem }
							/>
						) ) }
					</div>
				) }

				<Button
					variant="primary"
					onClick={ () => addItem( defaultMenuItem ) }
					style={ { marginTop: '16px' } }
				>
					{ __( 'Add Menu Item', 'portfolio' ) }
				</Button>
			</BaseControl>
		</BlockCard>
	);
}
