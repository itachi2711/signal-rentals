import { NgModule } from '@angular/core';
import { CommonModule, } from '@angular/common';
import { BrowserModule  } from '@angular/platform-browser';
import { Routes, RouterModule } from '@angular/router';
import { AdminLayoutComponent } from './layouts/admin-layout/admin-layout.component';
import { AuthGuard } from './authentication/auth.guard';
import { PermissionGuardService as PermGuard } from './authentication/permission-guard-service';
import { AuthGuardAdmin } from './authentication/auth.guard-admin';
import { AuthGuardLandlord } from './authentication/auth.guard-landlord';
import { AuthGuardTenant } from './authentication/auth.guard-tenant';
import { TenantDashResolverService } from './tenant-area/data/tenant-dash-resolver.service';
import { LandlordDashResolverService } from './landlord-area/data/landlord-dash-resolver.service';
import { AdminDashResolverService } from './dashboard/data/admin-dash-resolver.service';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'dashboard',
    pathMatch: 'full'
  },
  {
    path: '',
    component: AdminLayoutComponent,
    children: [
      {
        path: '',
        loadChildren: () => import('./layouts/admin-layout/admin-layout.module').then(m => m.AdminLayoutModule),
        resolve : {adminData: AdminDashResolverService},
        canActivate: [AuthGuardAdmin]
      },
      {
        path: 'landlords',
        loadChildren: () => import('./landlords/landlord.module').then(m => m.LandlordModule),
        canActivate: [AuthGuardAdmin],
        canLoad: [PermGuard],
        data: {
          permissions: ['view-landlord', 'create-landlord', 'edit-landlord', 'delete-landlord']
        }
      },
      {
        path: 'properties',
        loadChildren: () => import('./properties/property.module').then(m => m.PropertyModule),
        canActivate: [AuthGuard],
        canLoad: [PermGuard],
        data: {
          permissions: ['view-property', 'create-property', 'edit-property', 'delete-property', 'am-landlord']
        }
      },
      {
        path: 'tenants',
        loadChildren: () => import('./tenants/tenant.module').then(m => m.TenantModule),
        canActivate: [AuthGuardAdmin],
        canLoad: [PermGuard],
        data: {
          permissions: ['view-tenant', 'create-tenant', 'edit-tenant', 'delete-tenant']
        }
      },
      {
        path: 'leases',
        loadChildren: () => import('./leases/lease.module').then(m => m.LeaseModule),
        canActivate: [AuthGuard],
        canLoad: [PermGuard],
        data: {
          permissions: ['view-lease', 'create-lease', 'edit-lease', 'delete-lease', 'am-tenant', 'am-landlord']
        }
      },
      {
        path: 'readings',
        loadChildren: () => import('./readings/reading.module').then(m => m.ReadingModule),
        canActivate: [AuthGuardAdmin],
        canLoad: [PermGuard],
        data: {
          permissions: ['view-reading', 'create-reading', 'edit-reading', 'delete-reading']
        }
      },
      {
        path: 'invoices',
        loadChildren: () => import('./invoices/invoice.module').then(m => m.InvoiceModule),
        canActivate: [AuthGuard],
        canLoad: [PermGuard],
        data: {
          permissions: ['view-invoice', 'am-landlord']
        }
      },
      {
        path: 'payments',
        loadChildren: () => import('./payments/payment.module').then(m => m.PaymentModule),
        canActivate: [AuthGuard],
        canLoad: [PermGuard],
        data: {
          permissions: ['view-payment', 'approve-payment', 'cancel-payment', 'am-tenant', 'am-landlord']
        }
      },
      {
        path: 'settings',
        loadChildren: () => import('./settings/setting.module').then(m => m.SettingModule),
        canActivate: [AuthGuardAdmin],
        canLoad: [PermGuard],
        data: {
          permissions: ['manage-setting']
        }
      },
      {
        path: 'reports',
        loadChildren: () => import('./accounting/accounting.module').then(m => m.AccountingModule),
        canActivate: [AuthGuardAdmin],
        canLoad: [PermGuard],
        data: {
          permissions: ['view-report']
        }
       // data: { preload: true, delay: true }
      },
      {
        path: 'profile',
        loadChildren: () => import('./profile/user-profile.module').then(m => m.UserProfileModule),
        canActivate: [AuthGuardAdmin],
        canLoad: [PermGuard],
        data: {
          permissions: ['edit-profile']
        }
      },
      {
        path: 'notices',
        loadChildren: () => import('./vacate/vacate.module').then(m => m.VacateModule),
        canActivate: [AuthGuard],
        canLoad: [PermGuard],
        data: {
          permissions: ['view-notice', 'create-notice', 'edit-notice', 'delete-notice', 'am-tenant', 'am-landlord']
        }
      },
      {
        path: 'landlord/dashboard',
        loadChildren: () => import('./landlord-area/landlord-area.module').then(m => m.LandlordAreaModule),
        resolve : {landlordData: LandlordDashResolverService},
        canActivate: [AuthGuardLandlord],
        canLoad: [PermGuard],
        data: {
          permissions: ['am-landlord']
        }
      },
      {
        path: 'landlord/profile',
        loadChildren: () => import('./landlord-area/profile/landlord-profile.module')
            .then(m => m.LandlordProfileModule),
        canActivate: [AuthGuardLandlord],
        canLoad: [PermGuard],
        data: {
          permissions: ['am-landlord']
        }
      },
      {
        path: 'tenant/dashboard',
        loadChildren: () => import('./tenant-area/tenant-area.module').then(m => m.TenantAreaModule),
        resolve : {tenantData: TenantDashResolverService},
        canActivate: [AuthGuardTenant],
        canLoad: [PermGuard],
        data: {
          permissions: ['am-tenant']
        }
      },
      {
        path: 'tenant/profile',
        loadChildren: () => import('./tenant-area/profile/tenant-profile.module')
            .then(m => m.TenantProfileModule),
        canActivate: [AuthGuardTenant],
        canLoad: [PermGuard],
        data: {
          permissions: ['am-tenant']
        }
      },
    ]
  }
];

@NgModule({
  imports: [
    CommonModule,
    BrowserModule,
    RouterModule.forRoot(routes, {
    useHash: true,
    relativeLinkResolution: 'legacy'
})
  ],
  exports: [
  ],
})
export class AppRoutingModule { }
