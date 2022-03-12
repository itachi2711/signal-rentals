import { Routes, RouterModule } from '@angular/router';
import { UserProfileComponent } from './user-profile.component';
import { UserProfileResolverService } from './data/user-profile-resolver.service';

export const ROUTES: Routes = [
    {
        path: '',
        component: UserProfileComponent,
        resolve: {
            profile: UserProfileResolverService
        }
    }
];
export const UserProfileRoutingModule = RouterModule.forChild(ROUTES);
