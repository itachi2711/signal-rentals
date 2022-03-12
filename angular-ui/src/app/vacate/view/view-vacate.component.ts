import { AfterViewInit, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { MatDialog, MatDialogConfig } from '@angular/material/dialog';
import { VacateModel } from '../models/vacate-model';
import { VacateService } from '../data/vacate.service';
import { AddVacateComponent } from '../add/add-vacate.component';

@Component({
    selector: 'robi-view-vacate',
    styleUrls: ['./view-vacate.component.scss'],
    templateUrl: './view-vacate.component.html'
})
export class ViewVacateComponent implements OnInit, AfterViewInit  {

    form: FormGroup;

    formErrors: any;

    loader = false;

    noticeID: string;
    noticeData$: Observable<VacateModel>;
    constructor(private fb: FormBuilder,
                private dialog: MatDialog,
                private notification: NotificationService,
                private vacateService: VacateService,
                private router: Router, private route: ActivatedRoute) {
    }

    ngOnInit() {
        this.noticeID = this.route.snapshot.paramMap.get('id');
        this.vacateService.selectedVacateNoticeChanges$.subscribe(data => {
            if (data) {
                this.noticeData$ = of(data);
            }
            if (!data) {
                this.vacateService.getById(this.noticeID).subscribe(notice => {
                    this.noticeData$ = of(notice);
                    this.vacateService.changeSelectedVacateNotice(notice);
                });
            }
        });
    }

    /**
     * Add dialog launch
     */
    addDialog(isAdd = true, vacateNotice?: VacateModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {vacateNotice,
            isAdd: isAdd
        };

        const dialogRef = this.dialog.open(AddVacateComponent, dialogConfig);
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
