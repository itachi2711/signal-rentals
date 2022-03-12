import { DefaultHttpUrlGenerator, HttpResourceUrls, normalizeRoot, Pluralizer } from '@ngrx/data';
import { Injectable } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class AppHttpUrlGenerator extends DefaultHttpUrlGenerator {

    constructor(private pluralize: Pluralizer) {
        super(pluralize);
    }

    public getResourceUrls(entityName: string, root: string): HttpResourceUrls {
        let resourceUrls = this.knownHttpResourceUrls[entityName];
        if (!resourceUrls) {
            const nRoot = normalizeRoot(root);
            resourceUrls = {
                entityResourceUrl: `${nRoot}/${this.pluralize.pluralize(
                    entityName
                )}`.toLowerCase(),
                collectionResourceUrl: `${nRoot}/${this.pluralize.pluralize(
                    entityName
                )}`.toLowerCase(),
            };
            this.registerHttpResourceUrls({ [entityName]: resourceUrls });
        }
        return resourceUrls;
    }
}
