import { __ } from '@wordpress/i18n';
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { Icon, chevronUpDown } from '@wordpress/icons';
import { useMemo } from '@wordpress/element';

export function TechnologyItem( { technology } ) {
	const {
		attributes,
		listeners,
		setNodeRef,
		transform,
		transition,
		isDragging,
	} = useSortable( { id: technology.id } );

	const style = useMemo(
		() => ( {
			transform: CSS.Transform.toString( transform ),
			transition,
			opacity: isDragging ? 0.5 : 1,
		} ),
		[ transform, transition, isDragging ]
	);

	const iconUrl = technology.icon_url || '';

	return (
		<div
			ref={ setNodeRef }
			style={ style }
			className="portfolio-technology-item"
		>
			<div className="portfolio-technology-item-content">
				<div
					className="portfolio-technology-item-drag-handle"
					{ ...attributes }
					{ ...listeners }
				>
					<Icon icon={ chevronUpDown } />
				</div>

				{ iconUrl && (
					<div className="portfolio-technology-item-icon">
						<img
							src={ iconUrl }
							alt={ technology.name }
							width={ 40 }
							height={ 40 }
						/>
					</div>
				) }

				<div className="portfolio-technology-item-info">
					<h3 className="portfolio-technology-item-name">
						{ technology.name }
					</h3>

					{ technology.description && (
						<p className="portfolio-technology-item-description">
							{ technology.description }
						</p>
					) }

					<div className="portfolio-technology-item-meta">
						<span className="portfolio-technology-item-count">
							{ __( 'Projects:', 'am-portfolio-theme' ) }{ ' ' }
							{ technology.count }
						</span>
						<span className="portfolio-technology-item-order">
							{ __( 'Order:', 'am-portfolio-theme' ) }{ ' ' }
							{ technology.order + 1 }
						</span>
					</div>
				</div>
			</div>
		</div>
	);
}
