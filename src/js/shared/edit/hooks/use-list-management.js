import { useCallback } from '@wordpress/element';

export const useListManagement = ( items, setAttributes, itemsKey ) => {
	const addItem = useCallback(
		( defaultItem = {} ) => {
			const newItems = [ ...items, defaultItem ];
			setAttributes( { [ itemsKey ]: newItems } );
		},
		[ items, setAttributes, itemsKey ]
	);

	const removeItem = useCallback(
		( index ) => {
			const newItems = [ ...items ];
			newItems.splice( index, 1 );
			setAttributes( { [ itemsKey ]: newItems } );
		},
		[ items, setAttributes, itemsKey ]
	);

	const updateItem = useCallback(
		( index, field, value ) => {
			const newItems = [ ...items ];
			newItems[ index ] = {
				...newItems[ index ],
				[ field ]: value,
			};
			setAttributes( { [ itemsKey ]: newItems } );
		},
		[ items, setAttributes, itemsKey ]
	);

	const moveItem = useCallback(
		( index, direction ) => {
			const isFirst = index === 0;
			const isLast = index === items.length - 1;

			if (
				( direction === 'up' && isFirst ) ||
				( direction === 'down' && isLast )
			) {
				return;
			}

			const newIndex = direction === 'up' ? index - 1 : index + 1;
			const newItems = [ ...items ];

			[ newItems[ index ], newItems[ newIndex ] ] = [
				newItems[ newIndex ],
				newItems[ index ],
			];
			setAttributes( { [ itemsKey ]: newItems } );
		},
		[ items, setAttributes, itemsKey ]
	);

	return {
		addItem,
		removeItem,
		updateItem,
		moveItem,
	};
};
