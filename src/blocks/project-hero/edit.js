import { __ } from '@wordpress/i18n';
import { BaseControl, Button, Flex, TextControl } from '@wordpress/components';
import './editor.scss';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockCard from '../../js/shared/edit/components/block-card';

const MetaItem = ( { item, index, metaItems, onUpdate, onRemove, onMove } ) => {
	const isFirst = index === 0;
	const isLast = index === metaItems.length - 1;

	return (
		<div className="meta-item">
			<Flex align="center" gap={ 3 } style={ { marginBottom: '12px' } }>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
				/>

				<TextControl
					label={ __( 'Label', 'am-portfolio-theme' ) }
					value={ item.label }
					onChange={ ( value ) => onUpdate( index, 'label', value ) }
					placeholder={ __( 'e.g., Role', 'am-portfolio-theme' ) }
				/>

				<TextControl
					label={ __( 'Value', 'am-portfolio-theme' ) }
					value={ item.value }
					onChange={ ( value ) => onUpdate( index, 'value', value ) }
					placeholder={ __(
						'e.g., Full-Stack Developer',
						'am-portfolio-theme'
					) }
				/>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-end', marginBottom: '8px' } }
				/>
			</Flex>

			{ ! isLast && <hr className="meta-item-separator" /> }
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const {
		meta_items: metaItems = [],
		live_project_url: liveProjectUrl,
		source_code_url: sourceCodeUrl,
	} = attributes;
	const { addItem, moveItem, removeItem, updateItem } = useListManagement(
		metaItems,
		setAttributes,
		'meta_items'
	);

	const defaultMetaItem = { label: '', value: '' };

	return (
		<BlockCard title={ __( 'Hero Section', 'am-portfolio-theme' ) }>
			<BaseControl
				id="project-hero-live-link"
				label={ __( 'Live Project Link URL', 'am-portfolio-theme' ) }
				help={ __(
					'Enter the URL for the live project.',
					'am-portfolio-theme'
				) }
			>
				<TextControl
					value={ liveProjectUrl }
					onChange={ ( value ) =>
						setAttributes( {
							live_project_url: value,
						} )
					}
					placeholder={ __(
						'https://example.com',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				id="project-hero-source-code"
				label={ __( 'Source Code URL', 'am-portfolio-theme' ) }
				help={ __(
					'Enter the URL for the source code repository.',
					'am-portfolio-theme'
				) }
			>
				<TextControl
					value={ sourceCodeUrl || '' }
					onChange={ ( value ) =>
						setAttributes( {
							source_code_url: value,
						} )
					}
					placeholder={ __(
						'https://github.com/user/repo',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				id="project-hero-meta-items"
				__nextHasNoMarginBottom
				label={ __( 'Meta Items', 'am-portfolio-theme' ) }
				help={ __(
					'Add project meta information (e.g., Role, Timeframe, Category).',
					'am-portfolio-theme'
				) }
			>
				{ metaItems.length > 0 && (
					<div className="meta-items-list">
						{ metaItems.map( ( item, index ) => (
							<MetaItem
								key={ index }
								item={ item }
								index={ index }
								metaItems={ metaItems }
								onUpdate={ updateItem }
								onRemove={ removeItem }
								onMove={ moveItem }
							/>
						) ) }
					</div>
				) }

				<Button
					variant="primary"
					onClick={ () => addItem( defaultMetaItem ) }
					style={ { marginTop: '16px' } }
				>
					{ __( 'Add Meta Item', 'am-portfolio-theme' ) }
				</Button>
			</BaseControl>
		</BlockCard>
	);
}
