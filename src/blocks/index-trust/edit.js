import { __ } from '@wordpress/i18n';
import { trash as trashIcon, arrowUp, arrowDown } from '@wordpress/icons';
import { useBlockProps } from '@wordpress/block-editor';
import {
	BaseControl,
	Button,
	Card,
	CardBody,
	CardHeader,
	Flex,
	FlexBlock,
	FlexItem,
	TextControl,
	Tooltip,
} from '@wordpress/components';
import './editor.scss';

const MoveButtons = ( { index, isFirst, isLast, onMove } ) => (
	<FlexItem style={ { minWidth: '60px' } }>
		<Flex gap={ 1 } justify="center">
			<Tooltip text={ __( 'Move up', 'portfolio' ) }>
				<Button
					size="small"
					icon={ arrowUp }
					onClick={ () => onMove( index, 'up' ) }
					disabled={ isFirst }
					variant="tertiary"
				/>
			</Tooltip>
			<Tooltip text={ __( 'Move down', 'portfolio' ) }>
				<Button
					size="small"
					icon={ arrowDown }
					onClick={ () => onMove( index, 'down' ) }
					disabled={ isLast }
					variant="tertiary"
				/>
			</Tooltip>
		</Flex>
	</FlexItem>
);

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

const RemoveButton = ( { index, onRemove } ) => (
	<FlexItem style={ { alignSelf: 'flex-end', marginBottom: '8px' } }>
		<Tooltip text={ __( 'Remove item', 'portfolio' ) }>
			<Button
				size="small"
				isDestructive
				icon={ trashIcon }
				onClick={ () => onRemove( index ) }
			/>
		</Tooltip>
	</FlexItem>
);

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

				<TextInput
					label={ __( 'Number', 'portfolio' ) }
					value={ item.number }
					onChange={ ( value ) => onUpdate( index, 'number', value ) }
					placeholder={ __( 'e.g., 100+', 'portfolio' ) }
				/>

				<TextInput
					label={ __( 'Label', 'portfolio' ) }
					value={ item.label }
					onChange={ ( value ) => onUpdate( index, 'label', value ) }
					placeholder={ __( 'e.g., Happy Clients', 'portfolio' ) }
				/>

				<RemoveButton index={ index } onRemove={ onRemove } />
			</Flex>

			{ ! isLast && <hr className="trust-item-separator" /> }
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const { trust_items: trustItems = [] } = attributes;

	const addTrustItem = () => {
		const newTrustItems = [ ...trustItems, { number: '', label: '' } ];
		setAttributes( { trust_items: newTrustItems } );
	};

	const removeTrustItem = ( index ) => {
		const newTrustItems = [ ...trustItems ];
		newTrustItems.splice( index, 1 );
		setAttributes( { trust_items: newTrustItems } );
	};

	const updateTrustItem = ( index, field, value ) => {
		const newTrustItems = [ ...trustItems ];
		newTrustItems[ index ] = {
			...newTrustItems[ index ],
			[ field ]: value,
		};
		setAttributes( { trust_items: newTrustItems } );
	};

	const moveTrustItem = ( index, direction ) => {
		const isFirst = index === 0;
		const isLast = index === trustItems.length - 1;

		if (
			( direction === 'up' && isFirst ) ||
			( direction === 'down' && isLast )
		) {
			return;
		}

		const newIndex = direction === 'up' ? index - 1 : index + 1;
		const newTrustItems = [ ...trustItems ];

		[ newTrustItems[ index ], newTrustItems[ newIndex ] ] = [
			newTrustItems[ newIndex ],
			newTrustItems[ index ],
		];

		setAttributes( { trust_items: newTrustItems } );
	};

	const blockProps = useBlockProps( {
		style: {
			paddingLeft: '24px',
			paddingRight: '24px',
			marginBottom: '24px',
		},
	} );

	return (
		<div { ...blockProps }>
			<Card style={ { width: '100%' } }>
				<CardHeader>
					<h4>{ __( 'Trust Items', 'portfolio' ) }</h4>
				</CardHeader>
				<CardBody>
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
										onUpdate={ updateTrustItem }
										onRemove={ removeTrustItem }
										onMove={ moveTrustItem }
									/>
								) ) }
							</div>
						) }

						<Button
							variant="primary"
							onClick={ addTrustItem }
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
								{ __(
									'Maximum 3 trust items allowed',
									'portfolio'
								) }
							</p>
						) }
					</BaseControl>
				</CardBody>
			</Card>
		</div>
	);
}
