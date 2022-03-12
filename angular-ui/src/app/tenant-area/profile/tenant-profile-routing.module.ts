import { Routes, RouterModule } from '@angular/router';
import { TenantProfileComponent } from './tenant-profile.component';
import { TenantProfileResolverService } from './data/tenant-profile-resolver.service';

export const ROUTES: Routes = [
    {
        path: '',
        component: TenantProfileComponent,
        resolve: {
            profile: TenantProfileResolverService
        }
    }
];


export const TenantProfileRoutingModule = RouterModule.forChild(ROUTES);
