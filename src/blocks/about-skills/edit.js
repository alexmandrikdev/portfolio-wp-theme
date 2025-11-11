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
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import './editor.scss';
import RemoveButton from '../../js/shared/edit/components/remove-button';
import MoveButtons from '../../js/shared/edit/components/move-buttons';
import { useListManagement } from '../../js/shared/edit/hooks/use-list-management';
import BlockCard from '../../js/shared/edit/components/block-card';

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
											alt=""
											style={ {
												display: 'block',
												marginBottom: '8px',
												width: '60px',
												height: '60px',
												objectFit: 'contain',
											} }
										/>
										<Button
											variant="secondary"
											onClick={ open }
										>
											{ __(
												'Change Image',
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
											'Select Image',
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

const SkillItem = ( { skill, index, skills, onUpdate, onRemove, onMove } ) => {
	const isFirst = index === 0;
	const isLast = index === skills.length - 1;

	return (
		<div className="skill-item">
			<Flex align="center" gap={ 3 } style={ { marginBottom: '12px' } }>
				<MoveButtons
					index={ index }
					isFirst={ isFirst }
					isLast={ isLast }
					onMove={ onMove }
				/>
				<FlexBlock>
					<TextControl
						label={ __( 'Skill', 'am-portfolio-theme' ) }
						value={ skill }
						onChange={ ( value ) => onUpdate( index, value ) }
						placeholder={ __(
							'e.g., HTML5 & CSS3',
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
			{ ! isLast && <hr className="skill-item-separator" /> }
		</div>
	);
};

const SkillCategoryItem = ( {
	category,
	index,
	categories,
	setAttributes,
} ) => {
	const { moveItem, removeItem } = useListManagement(
		categories,
		setAttributes,
		'skill_categories'
	);

	const updateCategoryField = ( field, value ) => {
		const updatedCategories = [ ...categories ];
		updatedCategories[ index ] = {
			...updatedCategories[ index ],
			[ field ]: value,
		};
		setAttributes( { skill_categories: updatedCategories } );
	};

	const updateSkillItem = ( skillIndex, value ) => {
		const updatedSkills = [ ...( category.skills || [] ) ];
		updatedSkills[ skillIndex ] = value;
		updateCategoryField( 'skills', updatedSkills );
	};

	const categoryIsFirst = index === 0;
	const categoryIsLast = index === categories.length - 1;

	return (
		<div className="skill-category-item">
			<Card style={ { width: '100%', marginBottom: '16px' } }>
				<CardHeader>
					<Flex align="center" gap={ 3 }>
						<MoveButtons
							index={ index }
							isFirst={ categoryIsFirst }
							isLast={ categoryIsLast }
							onMove={ moveItem }
						/>
						<h4>
							{ __( 'Skill Category', 'am-portfolio-theme' ) } #
							{ index + 1 }
						</h4>
						<RemoveButton
							index={ index }
							onRemove={ removeItem }
							style={ { marginLeft: 'auto' } }
						/>
					</Flex>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 4 }>
						<MediaUploadField
							label={ __(
								'Category Icon',
								'am-portfolio-theme'
							) }
							value={ category.icon }
							onChange={ ( value ) =>
								updateCategoryField( 'icon', value )
							}
						/>
						<TextInput
							label={ __(
								'Category Title',
								'am-portfolio-theme'
							) }
							value={ category.title }
							onChange={ ( value ) =>
								updateCategoryField( 'title', value )
							}
							placeholder={ __(
								'e.g., Frontend',
								'am-portfolio-theme'
							) }
						/>
						<BaseControl
							id={ `skill-category-${ index }-skills` }
							__nextHasNoMarginBottom
							label={ __( 'Skills', 'am-portfolio-theme' ) }
							help={ __(
								'Add skills for this category.',
								'am-portfolio-theme'
							) }
						>
							{ category.skills && category.skills.length > 0 && (
								<div className="skills-list">
									{ category.skills.map(
										( skill, skillIndex ) => (
											<SkillItem
												key={ skillIndex }
												skill={ skill }
												index={ skillIndex }
												skills={ category.skills }
												onUpdate={ updateSkillItem }
												onRemove={ ( removeIndex ) => {
													const updatedSkills =
														category.skills.filter(
															( _, i ) =>
																i !==
																removeIndex
														);
													updateCategoryField(
														'skills',
														updatedSkills
													);
												} }
												onMove={ (
													fromIndex,
													direction
												) => {
													const skillIsFirst =
														fromIndex === 0;
													const skillIsLast =
														fromIndex ===
														category.skills.length -
															1;

													if (
														( direction === 'up' &&
															skillIsFirst ) ||
														( direction ===
															'down' &&
															skillIsLast )
													) {
														return;
													}

													const newIndex =
														direction === 'up'
															? fromIndex - 1
															: fromIndex + 1;
													const updatedSkills = [
														...category.skills,
													];

													[
														updatedSkills[
															fromIndex
														],
														updatedSkills[
															newIndex
														],
													] = [
														updatedSkills[
															newIndex
														],
														updatedSkills[
															fromIndex
														],
													];
													updateCategoryField(
														'skills',
														updatedSkills
													);
												} }
											/>
										)
									) }
								</div>
							) }

							<Button
								variant="primary"
								onClick={ () => {
									const currentSkills = category.skills || [];
									updateCategoryField( 'skills', [
										...currentSkills,
										'',
									] );
								} }
								style={ { marginTop: '16px' } }
							>
								{ __( 'Add Skill', 'am-portfolio-theme' ) }
							</Button>
						</BaseControl>
					</Flex>
				</CardBody>
			</Card>
		</div>
	);
};
const SkillCategoriesEditor = ( { categories, setAttributes } ) => {
	const { addItem } = useListManagement(
		categories,
		setAttributes,
		'skill_categories'
	);

	const defaultCategory = {
		icon: 0,
		title: '',
		skills: [],
	};

	const canAddMore = categories.length < 4;

	return (
		<BaseControl
			id="about-skills-categories"
			__nextHasNoMarginBottom
			label={ __( 'Skill Categories', 'am-portfolio-theme' ) }
			help={ __(
				'Add up to 4 skill categories with icons, titles, and skills.',
				'am-portfolio-theme'
			) }
		>
			{ categories.length > 0 && (
				<div className="skill-categories-list">
					{ categories.map( ( category, index ) => (
						<SkillCategoryItem
							key={ index }
							category={ category }
							index={ index }
							categories={ categories }
							setAttributes={ setAttributes }
						/>
					) ) }
				</div>
			) }

			{ canAddMore && (
				<Button
					variant="primary"
					onClick={ () => addItem( defaultCategory ) }
					style={ { marginTop: '16px' } }
				>
					{ __( 'Add Skill Category', 'am-portfolio-theme' ) }
				</Button>
			) }

			{ ! canAddMore && (
				<p
					style={ {
						marginTop: '16px',
						color: '#757575',
						fontStyle: 'italic',
					} }
				>
					{ __(
						'Maximum of 4 skill categories reached.',
						'am-portfolio-theme'
					) }
				</p>
			) }
		</BaseControl>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const { heading = '', skill_categories: skillCategories = [] } = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'About Skills Section', 'am-portfolio-theme' ) }>
			<Flex direction="column" gap={ 4 }>
				<TextInput
					label={ __( 'Section Heading', 'am-portfolio-theme' ) }
					value={ heading }
					onChange={ ( value ) =>
						updateAttribute( 'heading', value )
					}
					placeholder={ __(
						'e.g., Technical Skills',
						'am-portfolio-theme'
					) }
				/>

				<SkillCategoriesEditor
					categories={ skillCategories }
					setAttributes={ setAttributes }
				/>
			</Flex>
		</BlockCard>
	);
}
