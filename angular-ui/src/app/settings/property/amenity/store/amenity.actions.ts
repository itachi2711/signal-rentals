import { createAction, props } from '@ngrx/store';
import { AmenityModel } from '../model/amenity-model';
import { PageQuery } from '../../utility/utility-setting.component';

export const loadAllAmenities = createAction(
    '[Amenities Resolver] Load All Amenities'
);

export const allAmenitiesLoaded = createAction(
    '[Load Amenities Effect] All Amenities Loaded',
    props<{amenities: any}>()
);

export const actionLoadPaginatedAmenities = createAction(
    '[Amenities Resolver] Load Paginated Amenities',
    props<{page: PageQuery}>()
);
