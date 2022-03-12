import { Routes, RouterModule } from '@angular/router';
import { LandlordProfileComponent } from './landlord-profile.component';
import { LandlordProfileResolverService } from './data/landlord-profile-resolver.service';

export const ROUTES: Routes = [
    {
        path: '',
        component: LandlordProfileComponent,
        resolve: {
            profile: LandlordProfileResolverService
        }
    }
];


export const LandlordProfileRoutingModule = RouterModule.forChild(ROUTES);
