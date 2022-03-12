import { AmenityModel, compare } from '../../model/amenity-model';
import { createEntityAdapter, EntityState } from '@ngrx/entity';
import { createReducer, on } from '@ngrx/store';
import { AmenityActions } from '../action-types';

export interface AmenityState extends EntityState<AmenityModel> {
    pagination: {};
}

export const adapter =  createEntityAdapter<AmenityModel>({
    sortComparer: compare
});

export const initialAmenityState = adapter.getInitialState({
    pagination: {}
});

export const amenitiesReducer = createReducer(
    initialAmenityState,
    on(AmenityActions.allAmenitiesLoaded, (state, action) => adapter.addMany(action.amenities['data'],
        {...state, pagination: action.amenities['meta']}))
);

export const {
    selectAll
} = adapter.getSelectors();
