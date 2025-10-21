import { __ } from '@wordpress/i18n';
import { arrowUp, arrowDown } from '@wordpress/icons';
import { Button, Flex, FlexItem, Tooltip } from '@wordpress/components';

const MoveButtons = ( { index, isFirst, isLast, onMove, style = {} } ) => (
	<FlexItem style={ style }>
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

export default MoveButtons;
