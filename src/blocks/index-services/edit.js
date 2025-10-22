import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	Card,
	CardBody,
	CardHeader,
	Flex,
	FlexItem,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import './editor.scss';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockContainer from '../../js/shared/edit/components/block-container';

const ServiceItem = ( { item, index, items, onUpdate, onRemove, onMove } ) => {
	const isFirst = index === 0;
	const isLast = index === items.length - 1;

	return (
		<div className="service-item">
			<Card style={ { marginBottom: '16px' } }>
				<CardHeader>
					<Flex align="center" justify="space-between">
						<FlexItem>
							<strong>
								{ __( 'Service Item', 'portfolio' ) } #
								{ index + 1 }
							</strong>
						</FlexItem>
						<FlexItem>
							<Flex gap={ 1 }>
								<MoveButtons
									index={ index }
									isFirst={ isFirst }
									isLast={ isLast }
									onMove={ onMove }
								/>
								<RemoveButton
									index={ index }
									onRemove={ onRemove }
								/>
							</Flex>
						</FlexItem>
					</Flex>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 3 }>
						<TextControl
							label={ __( 'Icon Class', 'portfolio' ) }
							value={ item.icon }
							onChange={ ( value ) =>
								onUpdate( index, 'icon', value )
							}
							placeholder={ __( 'e.g., 💻', 'portfolio' ) }
							help={ __( 'Add an emoji', 'portfolio' ) }
						/>

						<TextControl
							label={ __( 'Title', 'portfolio' ) }
							value={ item.title }
							onChange={ ( value ) =>
								onUpdate( index, 'title', value )
							}
							placeholder={ __(
								'e.g., Web Development',
								'portfolio'
							) }
						/>

						<TextareaControl
							label={ __( 'Description', 'portfolio' ) }
							value={ item.text }
							onChange={ ( value ) =>
								onUpdate( index, 'text', value )
							}
							placeholder={ __(
								'Service description…',
								'portfolio'
							) }
							rows={ 3 }
						/>
					</Flex>
				</CardBody>
			</Card>
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const { title = '', items = [] } = attributes;
	const { addItem, moveItem, removeItem, updateItem } = useListManagement(
		items,
		setAttributes,
		'items'
	);

	const defaultItem = {
		icon: '',
		title: '',
		text: '',
	};

	return (
		<BlockContainer>
			<Card style={ { width: '100%' } }>
				<CardHeader>
					<h4>{ __( 'Services Section', 'portfolio' ) }</h4>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 4 }>
						<TextControl
							label={ __( 'Section Title', 'portfolio' ) }
							value={ title }
							onChange={ ( value ) =>
								setAttributes( { title: value } )
							}
							placeholder={ __(
								'e.g., Our Services',
								'portfolio'
							) }
						/>

						<BaseControl
							id="service-items"
							__nextHasNoMarginBottom
							label={ __( 'Service Items', 'portfolio' ) }
							help={ __(
								'Add up to 3 service items with icons, titles and descriptions',
								'portfolio'
							) }
						>
							{ items.length > 0 && (
								<div className="service-items-list">
									{ items.map( ( item, index ) => (
										<ServiceItem
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

							<Button
								variant="primary"
								onClick={ () => {
									addItem( defaultItem );
								} }
								disabled={ items.length >= 3 }
								style={ { marginTop: '16px' } }
							>
								{ __( 'Add Service Item', 'portfolio' ) }
							</Button>

							{ items.length >= 3 && (
								<p
									style={ {
										color: '#cc1818',
										fontSize: '12px',
										marginTop: '8px',
									} }
								>
									{ __(
										'Maximum 3 service items allowed',
										'portfolio'
									) }
								</p>
							) }
						</BaseControl>
					</Flex>
				</CardBody>
			</Card>
		</BlockContainer>
	);
}
