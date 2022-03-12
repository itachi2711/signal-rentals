import { Component, OnInit } from '@angular/core';
import { NotificationService } from '../../../shared/notification.service';
import { Observable, of } from 'rxjs';
import { FormBuilder } from '@angular/forms';
import { MatDialog } from '@angular/material/dialog';
import { ActivatedRoute, Router } from '@angular/router';
import { VacateModel } from '../../models/vacate-model';
import { VacateService } from '../../data/vacate.service';

@Component({
    selector: 'robi-view-vacate-general',
    templateUrl: './view-vacate-general.component.html',
    styleUrls: ['./view-vacate-general.component.css']
})
export class ViewVacateGeneralComponent implements OnInit {

    memberData: any;
    memberId = '';
    memberData$: any;

    profilePicUrl: string;
    profilePicFileToUpload: File = null;

    imageToShow: any;

    loader = false;
    memberShipForm = false;

    landlord$: Observable<any>;

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
}
