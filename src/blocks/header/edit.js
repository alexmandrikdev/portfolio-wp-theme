import { __ } from '@wordpress/i18n';
import { trash as trashIcon } from '@wordpress/icons';
import {
	BaseControl,
	Button,
	Card,
	CardBody,
	CardHeader,
	ComboboxControl,
	Flex,
	FlexBlock,
	FlexItem,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';
import './editor.scss';
import BlockContainer from '../../js/shared/edit/components/block-container';

export default function Edit( { attributes, setAttributes } ) {
	const { menu_items: menuItems = [] } = attributes;

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

	const menuItemOptions = pages.map( ( page ) => {
		return {
			label: page.title.rendered,
			value: page.id,
		};
	} );

	const addMenuItem = () =>
		setAttributes( { menu_items: [ ...menuItems, '' ] } );

	const removeMenuItem = ( index ) => {
		const newMenuItems = [ ...menuItems ];
		newMenuItems.splice( index, 1 );
		setAttributes( { menu_items: newMenuItems } );
	};

	const updateMenuItem = ( index, value ) => {
		const newMenuItems = [ ...menuItems ];
		newMenuItems[ index ] = value;
		setAttributes( { menu_items: newMenuItems } );
	};

	return (
		<BlockContainer>
			<Card style={ { width: '100%' } }>
				<CardHeader>
					<h4>{ __( 'Header', 'portfolio' ) }</h4>
				</CardHeader>
				<CardBody>
					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Menu Items', 'portfolio' ) }
						id="header-menu-items"
					>
						{ menuItems.map( ( item, index ) => (
							<Flex align="center" key={ index }>
								<FlexBlock>
									<ComboboxControl
										key={ index }
										value={ item }
										onChange={ ( value ) => {
											updateMenuItem( index, value );
										} }
										options={ menuItemOptions }
									/>
								</FlexBlock>
								<FlexItem>
									<Button
										size="small"
										isDestructive
										icon={ trashIcon }
										onClick={ () =>
											removeMenuItem( index )
										}
									></Button>
								</FlexItem>
							</Flex>
						) ) }

						<Button variant="primary" onClick={ addMenuItem }>
							Add menu item
						</Button>
					</BaseControl>
				</CardBody>
			</Card>
		</BlockContainer>
	);
}
