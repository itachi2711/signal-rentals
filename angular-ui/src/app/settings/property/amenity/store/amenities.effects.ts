import { Injectable } from '@angular/core';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import { AmenityActions } from './action-types';
import { AmenityService } from '../data/amenity.service';
import { concatMap, map } from 'rxjs/operators';
import { allAmenitiesLoaded } from './amenity.actions';

@Injectable()
export class AmenitiesEffects {

    loadAmenities$ = createEffect(
        () => this.actions$.pipe(
            ofType(AmenityActions.loadAllAmenities),
            concatMap(action => this.amenitiesHttpService.getAll('', 0, 3)),
            map(amenities => allAmenitiesLoaded({amenities}))

        )
    );

    loadPaginatedAmenities$ = createEffect(
        () => this.actions$.pipe(
            ofType(AmenityActions.actionLoadPaginatedAmenities),
            concatMap(action => this.amenitiesHttpService.getAll('', action.page?.pageIndex, action.page?.pageSize)),
            map(amenities => allAmenitiesLoaded({amenities}))
        )
    );

    constructor (private actions$: Actions, private amenitiesHttpService: AmenityService) {}
}
