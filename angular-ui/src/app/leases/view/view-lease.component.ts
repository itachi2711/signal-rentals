import { AfterViewInit, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { LeaseModel } from '../models/lease-model';
import { MatDialog, MatDialogConfig } from '@angular/material/dialog';
import { AddLeaseComponent } from '../add/add-lease.component';
import { LeaseService } from '../data/lease.service';
import { TerminateLeaseComponent } from './terminate/terminate-lease.component';
import { select, Store } from '@ngrx/store';
import { AppState } from '../../reducers';
import { LandlordService } from '../../landlords/data/landlord.service';
import { selectorIsLandlord, selectorUserID } from '../../authentication/authentication.selectors';
import { AuthenticationService } from '../../authentication/authentication.service';

@Component({
    selector: 'robi-view-lease',
    styleUrls: ['./view-lease.component.scss'],
    templateUrl: './view-lease.component.html'
})
export class ViewLeaseComponent implements OnInit, AfterViewInit  {

    form: FormGroup;

    formErrors: any;

    loader = false;


    leaseID: string;
    leaseNumber: string;
    leaseData$: Observable<LeaseModel>;

    isLandlord = false;
    landlordID: string;
    isAdmin$: Observable<boolean>;
    constructor(private store: Store<AppState>,
                private landlordService: LandlordService,
                private fb: FormBuilder,
                private dialog: MatDialog,
                private notification: NotificationService,
                private leaseService: LeaseService,
                private authenticationService: AuthenticationService,
                private router: Router, private route: ActivatedRoute) {
        this.isAdmin$ = this.authenticationService.isAdmin();
        this.store.pipe(select(selectorIsLandlord)).subscribe(isLandlord => {
            if (isLandlord) {
                this.isLandlord = true;
                this.store.pipe(select(selectorUserID)).subscribe(userID => this.landlordID = userID);
            }
        });
    }

    ngOnInit() {
        this.leaseID = this.route.snapshot.paramMap.get('id');
        this.leaseService.selectedLeaseChanges$.subscribe(data => {
            if (data) {
                this.leaseData$ = of(data);
            }
            if (!data) {
                if (this.isLandlord) {
                    this.landlordService.getNestedById(this.landlordService.nestedLeaseUrl(this.landlordID, this.leaseID))
                        .subscribe(lease => {
                            this.leaseData$ = of(lease);
                            this.leaseService.changeSelectedLease(lease);
                        });
                } else {
                    this.leaseService.getById(this.leaseID).subscribe(lease => {
                        this.leaseData$ = of(lease);
                        this.leaseService.changeSelectedLease(lease);
                    });
                }
            }
        });
    }

    /**
     * Add dialog launch
     */
    addDialog(mode: string, property?: LeaseModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {property,
            mode: mode
        };

        const dialogRef = this.dialog.open(AddLeaseComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    // this.loadData();
                }
            }
        );
    }

    onOutletActivated(componentReference) {
    }

    ngAfterViewInit(): void {}

    /**
     * Add dialog launch
     */
    terminateLease(lease: LeaseModel) {
        this.leaseID = lease?.id;
        this.leaseNumber = lease?.lease_number;
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.width = 'auto';
        dialogConfig.height = 'auto';
        dialogConfig.data = {
            leaseNumber: lease?.lease_number,
            leaseID: lease?.id
        };

        const dialogRef = this.dialog.open(TerminateLeaseComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                   // this.loadData();
                }
            }
        );
    }

    onSelected(lease: LeaseModel) {
        this.leaseService.changeSelectedLease(lease);
    }
}
