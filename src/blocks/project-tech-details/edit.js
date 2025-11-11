import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	Flex,
	FlexBlock,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import './editor.scss';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockCard from '../../js/shared/edit/components/block-card';

const MediaUploadField = ( { label, value, onChange } ) => {
	const imageUrl = useSelect(
		( select ) => {
			if ( ! value ) {
				return '';
			}
			const image = select( 'core' ).getMedia( value );
			return image?.source_url || '';
		},
		[ value ]
	);

	return (
		<FlexBlock>
			<BaseControl
				id={ `media-upload-${ value || 'new' }` }
				label={ label }
			>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) => onChange( media.id ) }
						allowedTypes={ [ 'image' ] }
						value={ value }
						render={ ( { open } ) => (
							<div>
								{ value ? (
									<div>
										<img
											src={ imageUrl }
											alt={ __(
												'Challenge icon preview',
												'am-portfolio-theme'
											) }
											style={ {
												display: 'block',
												marginBottom: '8px',
											} }
											width={ 32 }
											height={ 32 }
										/>
										<Button
											variant="secondary"
											onClick={ open }
										>
											{ __(
												'Replace Icon',
												'am-portfolio-theme'
											) }
										</Button>
									</div>
								) : (
									<Button
										variant="secondary"
										onClick={ open }
									>
										{ __(
											'Select Icon',
											'am-portfolio-theme'
										) }
									</Button>
								) }
							</div>
						) }
					/>
				</MediaUploadCheck>
			</BaseControl>
		</FlexBlock>
	);
};

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

const TextareaInput = ( { label, value, onChange, placeholder } ) => (
	<FlexBlock>
		<TextareaControl
			label={ label }
			value={ value }
			onChange={ onChange }
			placeholder={ placeholder }
			rows={ 4 }
		/>
	</FlexBlock>
);

const ChallengeItem = ( {
	item,
	index,
	challenges,
	onUpdate,
	onRemove,
	onMove,
} ) => {
	const isFirst = index === 0;
	const isLast = index === challenges.length - 1;

	return (
		<div className="challenge-item">
			<Flex
				align="flex-start"
				gap={ 3 }
				style={ { marginBottom: '16px' } }
			>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
					style={ { alignSelf: 'flex-start', marginTop: '24px' } }
				/>

				<Flex direction="column" gap={ 3 } style={ { flex: 1 } }>
					<TextInput
						label={ __( 'Title', 'am-portfolio-theme' ) }
						value={ item.title || '' }
						onChange={ ( value ) =>
							onUpdate( index, 'title', value )
						}
						placeholder={ __(
							'e.g., Complex Animations',
							'am-portfolio-theme'
						) }
					/>

					<TextareaInput
						label={ __( 'Description', 'am-portfolio-theme' ) }
						value={ item.description || '' }
						onChange={ ( value ) =>
							onUpdate( index, 'description', value )
						}
						placeholder={ __(
							'Describe the challenge…',
							'am-portfolio-theme'
						) }
					/>

					<TextareaInput
						label={ __( 'Solution', 'am-portfolio-theme' ) }
						value={ item.solution || '' }
						onChange={ ( value ) =>
							onUpdate( index, 'solution', value )
						}
						placeholder={ __(
							'Describe the solution…',
							'am-portfolio-theme'
						) }
					/>
				</Flex>

				<MediaUploadField
					label={ __( 'Icon', 'am-portfolio-theme' ) }
					value={ item.icon || '' }
					onChange={ ( value ) => onUpdate( index, 'icon', value ) }
				/>

				<RemoveButton
					index={ index }
					onRemove={ onRemove }
					style={ { alignSelf: 'flex-start', marginTop: '24px' } }
				/>
			</Flex>

			{ ! isLast && <hr className="challenge-item-separator" /> }
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const { challenges = [] } = attributes;
	const { addItem, moveItem, removeItem, updateItem } = useListManagement(
		challenges,
		setAttributes,
		'challenges'
	);

	const defaultChallengeItem = {
		icon: '',
		title: '',
		description: '',
		solution: '',
	};

	return (
		<BlockCard
			title={ __( 'Project Technical Details', 'am-portfolio-theme' ) }
		>
			<BaseControl
				id="project-tech-details-challenges"
				__nextHasNoMarginBottom
				label={ __(
					'Key Challenges & Solutions',
					'am-portfolio-theme'
				) }
				help={ __(
					'Add key challenges encountered during the project and their solutions.',
					'am-portfolio-theme'
				) }
			>
				{ challenges.length > 0 && (
					<div className="challenges-list">
						{ challenges.map( ( item, index ) => (
							<ChallengeItem
								key={ index }
								item={ item }
								index={ index }
								challenges={ challenges }
								onUpdate={ updateItem }
								onRemove={ removeItem }
								onMove={ moveItem }
							/>
						) ) }
					</div>
				) }

				<Button
					variant="primary"
					onClick={ () => addItem( defaultChallengeItem ) }
					style={ { marginTop: '16px' } }
				>
					{ __( 'Add Challenge', 'am-portfolio-theme' ) }
				</Button>
			</BaseControl>
		</BlockCard>
	);
}
