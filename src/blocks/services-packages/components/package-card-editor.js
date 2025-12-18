import { Card, CardBody } from '@wordpress/components';
import { useListManagement } from '../../../js/shared/edit/hooks/use-list-management';
import PackageHeader from './package-header';
import PackageFields from './package-fields';

const PackageCardEditor = ( {
	packageData,
	index,
	packages,
	setAttributes,
} ) => {
	const { moveItem, removeItem } = useListManagement(
		packages,
		setAttributes,
		'package_cards'
	);

	const updatePackageField = ( field, value ) => {
		const updatedPackages = [ ...packages ];
		updatedPackages[ index ] = {
			...updatedPackages[ index ],
			[ field ]: value,
		};
		setAttributes( { package_cards: updatedPackages } );
	};

	const updateFeatureItem = ( featureIndex, value ) => {
		const updatedFeatures = [ ...( packageData.features || [] ) ];
		updatedFeatures[ featureIndex ] = value;
		updatePackageField( 'features', updatedFeatures );
	};

	const isFirst = index === 0;
	const isLast = index === packages.length - 1;

	return (
		<Card style={ { width: '100%', marginBottom: '24px' } }>
			<PackageHeader
				index={ index }
				isFirst={ isFirst }
				isLast={ isLast }
				onMove={ moveItem }
				onRemove={ removeItem }
			/>
			<CardBody>
				<PackageFields
					packageData={ packageData }
					index={ index }
					updatePackageField={ updatePackageField }
					updateFeatureItem={ updateFeatureItem }
				/>
			</CardBody>
		</Card>
	);
};

export default PackageCardEditor;
