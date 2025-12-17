import { __ } from '@wordpress/i18n';
import { BaseControl, Button, Flex, TextControl } from '@wordpress/components';
import './editor.scss';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockCard from '../../js/shared/edit/components/block-card';

const TrustItem = ( {
	item,
	index,
	trustItems,
	onUpdate,
	onRemove,
	onMove,
} ) => {
	const isFirst = index === 0;
	const isLast = index === trustItems.length - 1;

	return (
		<div className="trust-item">
			<Flex align="center" gap={ 3 } style={ { marginBottom: '12px' } }>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
				/>

				<TextControl
					label={ __( 'Number', 'portfolio' ) }
					value={ item.number }
					onChange={ ( value ) => onUpdate( index, 'number', value ) }
					placeholder={ __( 'e.g., 100+', 'portfolio' ) }
				/>

				<TextControl
					label={ __( 'Label', 'portfolio' ) }
					value={ item.label }
					onChange={ ( value ) => onUpdate( index, 'label', value ) }
					placeholder={ __( 'e.g., Happy Clients', 'portfolio' ) }
				/>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-end', marginBottom: '8px' } }
				/>
			</Flex>

			{ ! isLast && <hr className="trust-item-separator" /> }
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const { trust_items: trustItems = [] } = attributes;
	const { addItem, moveItem, removeItem, updateItem } = useListManagement(
		trustItems,
		setAttributes,
		'trust_items'
	);

	const defaultItem = { number: '', label: '' };

	return (
		<BlockCard title={ __( 'Trust Items', 'portfolio' ) }>
			<BaseControl
				id="trust-items"
				__nextHasNoMarginBottom
				label={ __( 'Trust Items', 'portfolio' ) }
				help={ __(
					'Add up to 3 trust items with numbers and labels',
					'portfolio'
				) }
			>
				{ trustItems.length > 0 && (
					<div className="trust-items-list">
						{ trustItems.map( ( item, index ) => (
							<TrustItem
								key={ index }
								item={ item }
								index={ index }
								trustItems={ trustItems }
								onUpdate={ updateItem }
								onRemove={ removeItem }
								onMove={ moveItem }
							/>
						) ) }
					</div>
				) }

				<Button
					variant="primary"
					onClick={ () => addItem( defaultItem ) }
					disabled={ trustItems.length >= 3 }
					style={ { marginTop: '16px' } }
				>
					{ __( 'Add Trust Item', 'portfolio' ) }
				</Button>

				{ trustItems.length >= 3 && (
					<p
						style={ {
							color: '#cc1818',
							fontSize: '12px',
							marginTop: '8px',
						} }
					>
						{ __( 'Maximum 3 trust items allowed', 'portfolio' ) }
					</p>
				) }
			</BaseControl>
		</BlockCard>
	);
}
