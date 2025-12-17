import { __ } from '@wordpress/i18n';
import { CardHeader, Flex } from '@wordpress/components';
import MoveButtons from '../../../js/shared/edit/components/move-buttons';
import RemoveButton from '../../../js/shared/edit/components/remove-button';

const PackageHeader = ( { index, isFirst, isLast, onMove, onRemove } ) => {
	return (
		<CardHeader>
			<Flex align="center" gap={ 3 }>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
				/>
				<h4>
					{ __( 'Package Card', 'am-portfolio-theme' ) } { index + 1 }
				</h4>
				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { marginLeft: 'auto' } }
				/>
			</Flex>
		</CardHeader>
	);
};

export default PackageHeader;
