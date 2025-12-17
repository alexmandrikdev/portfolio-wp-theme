import { __ } from '@wordpress/i18n';
import { BaseControl, Button, Flex, TextControl } from '@wordpress/components';
import { RichText } from '@wordpress/block-editor';
import './editor.scss';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockCard from '../../js/shared/edit/components/block-card';

const FAQItem = ( { item, index, items, onUpdate, onRemove, onMove } ) => {
	const isFirst = index === 0;
	const isLast = index === items.length - 1;

	return (
		<div className="faq-item">
			<Flex
				align="flex-start"
				gap={ 3 }
				style={ { marginBottom: '24px' } }
			>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
					style={ { alignSelf: 'flex-start', marginTop: '24px' } }
				/>

				<Flex direction="column" gap={ 3 } style={ { flex: 1 } }>
					<TextControl
						label={ __( 'Question', 'am-portfolio-theme' ) }
						value={ item.question || '' }
						onChange={ ( value ) =>
							onUpdate( index, 'question', value )
						}
						placeholder={ __(
							'e.g., What services do you offer?',
							'am-portfolio-theme'
						) }
					/>

					<BaseControl
						id={ `faq-answer-${ index }` }
						__nextHasNoMarginBottom
						label={ __( 'Answer', 'am-portfolio-theme' ) }
					>
						<RichText
							style={ {
								border: '1px solid #949494',
								borderRadius: '2px',
								padding: '6px 8px',
							} }
							tagName="p"
							value={ item.answer || '' }
							onChange={ ( value ) =>
								onUpdate( index, 'answer', value )
							}
							placeholder={ __(
								'Provide a detailed answer to the question…',
								'am-portfolio-theme'
							) }
							allowedFormats={ [
								'core/bold',
								'core/italic',
								'core/link',
								'core/strikethrough',
							] }
						/>
					</BaseControl>
				</Flex>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-start', marginTop: '24px' } }
				/>
			</Flex>
			{ ! isLast && <hr className="faq-item-separator" /> }
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

	const defaultFAQItem = { question: '', answer: '' };

	return (
		<BlockCard title={ __( 'FAQ Section', 'am-portfolio-theme' ) }>
			<TextControl
				label={ __( 'Section Title', 'am-portfolio-theme' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				placeholder={ __(
					'e.g., Frequently Asked Questions',
					'am-portfolio-theme'
				) }
			/>

			<BaseControl
				id="faq-items"
				__nextHasNoMarginBottom
				label={ __( 'FAQ Items', 'am-portfolio-theme' ) }
				help={ __(
					'Add frequently asked questions and their answers.',
					'am-portfolio-theme'
				) }
			>
				{ items.length > 0 && (
					<div className="faq-items-list">
						{ items.map( ( item, index ) => (
							<FAQItem
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
					onClick={ () => addItem( defaultFAQItem ) }
					style={ { marginTop: '16px' } }
				>
					{ __( 'Add FAQ Item', 'am-portfolio-theme' ) }
				</Button>
			</BaseControl>
		</BlockCard>
	);
}
