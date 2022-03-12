import { Directive, ElementRef, Input, OnInit, TemplateRef, ViewContainerRef } from '@angular/core';
import { select, Store } from '@ngrx/store';
import { AppState } from '../../reducers';
import { selectorUserScopes } from '../../authentication/authentication.selectors';

@Directive({
    selector: '[robiPermission]'
})
export class HasPermissionDirective implements OnInit {
    private userScopes;
    private permissions = [];

    constructor(
        private element: ElementRef,
        private templateRef: TemplateRef<any>,
        private viewContainer: ViewContainerRef,
        private store: Store<AppState>
    ) {
    }

    ngOnInit() {
        this.store.pipe(select(selectorUserScopes)).subscribe(scopes => {
            this.userScopes = scopes;
            this.updateView();
        });
    }

    @Input()
    set robiPermission(val) {
        this.permissions = val;
        this.updateView();
    }

    private updateView() {
        if (this.checkPermission()) {
            this.viewContainer.createEmbeddedView(this.templateRef);
        } else {
            this.viewContainer.clear();
        }
    }

    private checkPermission() {
        let hasPermission = false;

        if (this.userScopes && this.permissions !== undefined ) {
            for (const checkPermission of this.permissions) {
                const permissionFound = this.userScopes.find(x => x.toUpperCase() === checkPermission.toUpperCase());
                if (permissionFound) {
                    hasPermission = true;
                }
            }
        }
        return hasPermission;
    }
}
