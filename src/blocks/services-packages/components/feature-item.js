import { __ } from '@wordpress/i18n';
import { Flex, FlexBlock, TextControl } from '@wordpress/components';
import RemoveButton from '../../../js/shared/edit/components/remove-button';
import MoveButtons from '../../../js/shared/edit/components/move-buttons';

const FeatureItem = ( { item, index, items, onUpdate, onRemove, onMove } ) => {
	const isFirst = index === 0;
	const isLast = index === items.length - 1;

	return (
		<div className="feature-item">
			<Flex align="center" gap={ 3 } style={ { marginBottom: '12px' } }>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
				/>

				<FlexBlock>
					<TextControl
						label={ __( 'Feature', 'am-portfolio-theme' ) }
						value={ item }
						onChange={ ( value ) => onUpdate( index, value ) }
						placeholder={ __(
							'e.g., Fully responsive design',
							'am-portfolio-theme'
						) }
					/>
				</FlexBlock>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-end', marginBottom: '8px' } }
				/>
			</Flex>
			{ ! isLast && <hr className="feature-item-separator" /> }
		</div>
	);
};

export default FeatureItem;
