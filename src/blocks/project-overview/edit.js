import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	Card,
	CardBody,
	CardHeader,
	Flex,
	FlexBlock,
	TextControl,
} from '@wordpress/components';
import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockContainer from '../../js/shared/edit/components/block-container';

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

const BulletItem = ( { item, index, items, onUpdate, onRemove, onMove } ) => {
	const isFirst = index === 0;
	const isLast = index === items.length - 1;

	return (
		<div className="bullet-item">
			<Flex align="center" gap={ 3 } style={ { marginBottom: '12px' } }>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
				/>

				<TextInput
					label={ __( 'Item Text', 'am-portfolio-theme' ) }
					value={ item.text }
					onChange={ ( value ) => onUpdate( index, 'text', value ) }
					placeholder={ __(
						'e.g., Fully responsive design',
						'am-portfolio-theme'
					) }
				/>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-end', marginBottom: '8px' } }
				/>
			</Flex>
			{ ! isLast && <hr className="bullet-item-separator" /> }
		</div>
	);
};

const CardEditor = ( { title, items, setAttributes, attributeName } ) => {
	const { addItem, moveItem, removeItem, updateItem } = useListManagement(
		items,
		setAttributes,
		attributeName
	);

	const defaultBulletItem = { text: '' };

	return (
		<Card style={ { width: '100%', marginBottom: '16px' } }>
			<CardHeader>
				<h4>{ title }</h4>
			</CardHeader>
			<CardBody>
				<BaseControl
					id={ `project-overview-${ attributeName }-items` }
					__nextHasNoMarginBottom
					label={ __( 'Items', 'am-portfolio-theme' ) }
					help={ __(
						'Add bullet points for this card.',
						'am-portfolio-theme'
					) }
				>
					{ items.length > 0 && (
						<div className="bullet-items-list">
							{ items.map( ( item, index ) => (
								<BulletItem
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
						onClick={ () => addItem( defaultBulletItem ) }
						style={ { marginTop: '16px' } }
					>
						{ __( 'Add Item', 'am-portfolio-theme' ) }
					</Button>
				</BaseControl>
			</CardBody>
		</Card>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const {
		task_card_items: taskCardItems = [],
		solution_card_items: solutionCardItems = [],
	} = attributes;

	const blockProps = useBlockProps();

	return (
		<BlockContainer>
			<div { ...blockProps }>
				<Card style={ { width: '100%' } }>
					<CardHeader>
						<h4>
							{ __( 'Overview Section', 'am-portfolio-theme' ) }
						</h4>
					</CardHeader>
					<CardBody>
						<CardEditor
							title={ __( 'Task Card', 'am-portfolio-theme' ) }
							items={ taskCardItems }
							setAttributes={ setAttributes }
							attributeName="task_card_items"
						/>
						<CardEditor
							title={ __(
								'Solution Card',
								'am-portfolio-theme'
							) }
							items={ solutionCardItems }
							setAttributes={ setAttributes }
							attributeName="solution_card_items"
						/>
					</CardBody>
				</Card>
			</div>
		</BlockContainer>
	);
}
