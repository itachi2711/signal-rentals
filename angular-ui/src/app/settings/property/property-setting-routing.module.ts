import { Routes, RouterModule } from '@angular/router';
import { PropertySettingComponent } from './property-setting.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: PropertySettingComponent,
        children: [
            {
                path: '',
          /*      loadChildren: () => import('app/settings/property/general/property-general-setting.module')
                    .then(m => m.PropertyGeneralSettingModule)*/

                loadChildren: () => import('app/settings/property/type/property-type-setting.module')
                    .then(m => m.PropertyTypeSettingModule)
            },
            /*{
                path: 'property_types',
                loadChildren: () => import('app/settings/property/type/property-type-setting.module')
                    .then(m => m.PropertyTypeSettingModule)
            },*/
            {
                path: 'amenities',
                loadChildren: () => import('app/settings/property/amenity/amenity-setting.module')
                    .then(m => m.AmenitySettingModule)
            },
            {
                path: 'utilities',
                loadChildren: () => import('app/settings/property/utility/utility-setting.module')
                    .then(m => m.UtilitySettingModule)
            },
            {
                path: 'unit_types',
                loadChildren: () => import('app/settings/property/unit-type/unit-type-setting.module')
                    .then(m => m.UnitTypeSettingModule),
            }
        ]
    }
];

export const PropertySettingRoutingModule = RouterModule.forChild(ROUTES);
