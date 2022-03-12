import { AfterViewInit, Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { LandlordModel } from '../models/landlord-model';
import { MatDialog, MatDialogConfig } from '@angular/material/dialog';
import { AddLandlordComponent } from '../add/add-landlord.component';
import { LandlordService } from '../data/landlord.service';

@Component({
    selector: 'robi-view-landlord',
    styleUrls: ['./view-landlord.component.scss'],
    templateUrl: './view-landlord.component.html'
})
export class ViewLandlordComponent implements OnInit, AfterViewInit  {
    landlordID: string;
    landlord$: Observable<any>;
    landlordData$: Observable<LandlordModel>;

    constructor(private dialog: MatDialog,
                private landlordService: LandlordService,
                private router: Router, private route: ActivatedRoute) {
    }

    ngOnInit() {
        this.landlordID = this.route.snapshot.paramMap.get('id');
        this.landlordService.selectedLandlordChanges$.subscribe(data => {
            if (data) {
                this.landlordData$ = of(data);
            }
            if (!data) {
                this.landlordService.getById(this.landlordID).subscribe(landlord => {
                    this.landlordData$ = of(landlord);
                    this.landlordService.changeSelectedLandlord(landlord);
                });
            }
        });
    }

    /**
     * Add dialog launch
     */
    addDialog(mode: string, landlord?: LandlordModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {landlord,
            mode: mode
        };

        const dialogRef = this.dialog.open(AddLandlordComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    // this.loadData();
                }
            }
        );
    }

    ngAfterViewInit(): void {}

    onSelected(landlord: LandlordModel): void {
        this.landlordService.changeSelectedLandlord(landlord);
    }
}
