import { __ } from '@wordpress/i18n';
import {
	DndContext,
	closestCenter,
	KeyboardSensor,
	PointerSensor,
	useSensor,
	useSensors,
} from '@dnd-kit/core';
import {
	arrayMove,
	SortableContext,
	sortableKeyboardCoordinates,
	verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import { restrictToVerticalAxis } from '@dnd-kit/modifiers';
import { TechnologyItem } from './technology-item';

export function TechnologyList( { technologies, onOrderChange } ) {
	const sensors = useSensors(
		useSensor( PointerSensor, {
			activationConstraint: {
				distance: 8,
			},
		} ),
		useSensor( KeyboardSensor, {
			coordinateGetter: sortableKeyboardCoordinates,
		} )
	);

	const handleDragEnd = ( event ) => {
		const { active, over } = event;

		if ( active.id !== over.id ) {
			const oldIndex = technologies.findIndex(
				( tech ) => tech.id === active.id
			);
			const newIndex = technologies.findIndex(
				( tech ) => tech.id === over.id
			);

			const newOrder = arrayMove( technologies, oldIndex, newIndex );
			onOrderChange( newOrder );
		}
	};

	return (
		<div className="portfolio-technology-list">
			<DndContext
				sensors={ sensors }
				collisionDetection={ closestCenter }
				onDragEnd={ handleDragEnd }
				modifiers={ [ restrictToVerticalAxis ] }
			>
				<SortableContext
					items={ technologies.map( ( tech ) => tech.id ) }
					strategy={ verticalListSortingStrategy }
				>
					<div className="portfolio-technology-list-items">
						{ technologies.map( ( technology ) => (
							<TechnologyItem
								key={ technology.id }
								technology={ technology }
							/>
						) ) }
					</div>
				</SortableContext>
			</DndContext>

			{ technologies.length === 0 && (
				<div className="portfolio-technology-list-empty">
					<p>
						{ __( 'No technologies found.', 'am-portfolio-theme' ) }
					</p>
				</div>
			) }
		</div>
	);
}
