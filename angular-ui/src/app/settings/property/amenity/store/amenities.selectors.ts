import { createFeatureSelector, createSelector } from '@ngrx/store';
import { AmenityState } from './reducers/amenity.reducers';
import * as fromAmenities from './reducers/amenity.reducers';
import { PageQuery } from '../../utility/utility-setting.component';
import { utilitySelectors } from '../../utility/data/ulitity-selectors';

export const selectAmenitiesState = createFeatureSelector<AmenityState>('amenities');

export const selectAllAmenities = createSelector(
    selectAmenitiesState,
    fromAmenities.selectAll
);

export const selectorAmenityPage = (page: PageQuery) => createSelector(
    selectAllAmenities,
    (amenities) => {
        const start = page.pageIndex * page.pageSize,
            end = start + page.pageSize;
        return amenities.slice(start, end);
    }
);

export const selectorAmenityPagination = createSelector(
    selectAmenitiesState,
    (amenities) => amenities?.pagination
);
