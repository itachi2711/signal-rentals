import { Action } from '@ngrx/store';
import { DefaultPersistenceResultHandler, EntityAction } from '@ngrx/data';
import { Injectable } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class PagePersistenceResultHandler extends DefaultPersistenceResultHandler {

    handleSuccess(originalAction: EntityAction): (data: any) => Action {
        const actionHandler = super.handleSuccess(originalAction);
        return (data: any) => {
            const action = actionHandler(data);
            if (action && data && data.meta) {
                (action as any).payload.meta = data.meta;
            }
            return action;
        };

    }
}
