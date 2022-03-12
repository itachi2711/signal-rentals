import { AfterViewInit, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { MatDialog, MatDialogConfig } from '@angular/material/dialog';
import { ReadingModel } from '../models/reading-model';
import { ReadingService } from '../data/reading.service';
import { AddReadingComponent } from '../add/add-reading.component';

@Component({
    selector: 'robi-view-reading',
    styleUrls: ['./view-reading.component.scss'],
    templateUrl: './view-reading.component.html'
})
export class ViewReadingComponent implements OnInit, AfterViewInit  {

    form: FormGroup;

    formErrors: any;

    loader = false;

    readingID: string;
    readingData$: Observable<ReadingModel>;
    constructor(private fb: FormBuilder,
                private dialog: MatDialog,
                private notification: NotificationService,
                private readingService: ReadingService,
                private router: Router, private route: ActivatedRoute) {
    }

    ngOnInit() {
        this.readingID = this.route.snapshot.paramMap.get('id');
        this.readingService.selectedReadingChanges$.subscribe(data => {
            if (data) {
                this.readingData$ = of(data);
            }
            if (!data) {
                this.readingService.getById(this.readingID).subscribe(reading => {
                    this.readingData$ = of(reading);
                    this.readingService.changeSelectedReading(reading);
                });
            }
        });
    }

    /**
     * Add dialog launch
     */
    addDialog(mode: string, reading?: ReadingModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {reading,
            mode: mode
        };

        const dialogRef = this.dialog.open(AddReadingComponent, dialogConfig);
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

}
