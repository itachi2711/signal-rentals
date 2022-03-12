import { EntitySelectorsFactory } from '@ngrx/data';
import { ActionReducerMap, createSelector } from '@ngrx/store';
import { UtilityModel } from '../model/utility-model';
import { PageQuery } from '../utility-setting.component';

export const utilitySelectors = new EntitySelectorsFactory().create<UtilityModel>('Utility');

export const selectorUtilityPage = (page: PageQuery) => createSelector(
    utilitySelectors.selectEntities,
    (utilities) => {
        const start = page.pageIndex * page.pageSize,
            end = start + page.pageSize;
        return utilities.slice(start, end);
    }
);

