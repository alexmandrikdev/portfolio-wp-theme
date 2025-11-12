import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Card,
	CardBody,
	CardHeader,
	Flex,
	FlexBlock,
	TextareaControl,
	TextControl,
} from '@wordpress/components';
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

export default function Edit( { attributes, setAttributes } ) {
	const {
		filter_toggle_text: filterToggleText,
		filter_title: filterTitle,
		project_type_title: projectTypeTitle,
		project_type_description: projectTypeDescription,
		all_types_label: allTypesLabel,
		technologies_title: technologiesTitle,
		clear_all_label: clearAllLabel,
		technologies_description: technologiesDescription,
		apply_filters_label: applyFiltersLabel,
		no_results_title: noResultsTitle,
		no_results_description: noResultsDescription,
	} = attributes;

	return (
		<BlockCard
			title={ __( 'Projects Listing Section', 'am-portfolio-theme' ) }
		>
			<Card style={ { width: '100%', marginBottom: '24px' } }>
				<CardHeader>
					<h4>{ __( 'Filter Settings', 'am-portfolio-theme' ) }</h4>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 4 }>
						<TextInput
							label={ __(
								'Filter Toggle Text',
								'am-portfolio-theme'
							) }
							value={ filterToggleText }
							onChange={ ( value ) =>
								setAttributes( { filter_toggle_text: value } )
							}
							placeholder={ __(
								'Filters',
								'am-portfolio-theme'
							) }
						/>
						<TextInput
							label={ __( 'Filter Title', 'am-portfolio-theme' ) }
							value={ filterTitle }
							onChange={ ( value ) =>
								setAttributes( { filter_title: value } )
							}
							placeholder={ __(
								'Filter projects',
								'am-portfolio-theme'
							) }
						/>
						<TextInput
							label={ __(
								'Project Type Title',
								'am-portfolio-theme'
							) }
							value={ projectTypeTitle }
							onChange={ ( value ) =>
								setAttributes( { project_type_title: value } )
							}
							placeholder={ __(
								'Project Type',
								'am-portfolio-theme'
							) }
						/>
						<TextInput
							label={ __(
								'Project Type Description',
								'am-portfolio-theme'
							) }
							value={ projectTypeDescription }
							onChange={ ( value ) =>
								setAttributes( {
									project_type_description: value,
								} )
							}
							placeholder={ __(
								'Select one project type',
								'am-portfolio-theme'
							) }
						/>
						<TextInput
							label={ __(
								'All Types Label',
								'am-portfolio-theme'
							) }
							value={ allTypesLabel }
							onChange={ ( value ) =>
								setAttributes( { all_types_label: value } )
							}
							placeholder={ __(
								'All Types',
								'am-portfolio-theme'
							) }
						/>
						<TextInput
							label={ __(
								'Technologies Title',
								'am-portfolio-theme'
							) }
							value={ technologiesTitle }
							onChange={ ( value ) =>
								setAttributes( { technologies_title: value } )
							}
							placeholder={ __(
								'Technologies',
								'am-portfolio-theme'
							) }
						/>
						<TextInput
							label={ __(
								'Clear All Label',
								'am-portfolio-theme'
							) }
							value={ clearAllLabel }
							onChange={ ( value ) =>
								setAttributes( { clear_all_label: value } )
							}
							placeholder={ __(
								'Clear all',
								'am-portfolio-theme'
							) }
						/>
						<TextInput
							label={ __(
								'Technologies Description',
								'am-portfolio-theme'
							) }
							value={ technologiesDescription }
							onChange={ ( value ) =>
								setAttributes( {
									technologies_description: value,
								} )
							}
							placeholder={ __(
								'Select multiple technologies',
								'am-portfolio-theme'
							) }
						/>
						<TextInput
							label={ __(
								'Apply Filters Label',
								'am-portfolio-theme'
							) }
							value={ applyFiltersLabel }
							onChange={ ( value ) =>
								setAttributes( { apply_filters_label: value } )
							}
							placeholder={ __(
								'Apply Filters',
								'am-portfolio-theme'
							) }
						/>
					</Flex>
				</CardBody>
			</Card>

			<Card style={ { width: '100%', marginBottom: '24px' } }>
				<CardHeader>
					<h4>
						{ __( 'No Results Settings', 'am-portfolio-theme' ) }
					</h4>
				</CardHeader>
				<CardBody>
					<Flex direction="column" gap={ 4 }>
						<TextInput
							label={ __(
								'No Results Title',
								'am-portfolio-theme'
							) }
							value={ noResultsTitle }
							onChange={ ( value ) =>
								setAttributes( { no_results_title: value } )
							}
							placeholder={ __(
								'No projects found',
								'am-portfolio-theme'
							) }
						/>
						<BaseControl
							id="no-results-description"
							__nextHasNoMarginBottom
							label={ __(
								'No Results Description',
								'am-portfolio-theme'
							) }
							help={ __(
								'Use [reset button: your text] to insert a reset button with custom text',
								'am-portfolio-theme'
							) }
						>
							<TextareaControl
								value={ noResultsDescription }
								onChange={ ( value ) =>
									setAttributes( {
										no_results_description: value,
									} )
								}
								placeholder={ __(
									"We couldn't find any projects matching your current filters. Try adjusting your selection or [reset button: reset all filters] to see all projects.",
									'am-portfolio-theme'
								) }
								rows={ 2 }
							/>
						</BaseControl>
					</Flex>
				</CardBody>
			</Card>
		</BlockCard>
	);
}
