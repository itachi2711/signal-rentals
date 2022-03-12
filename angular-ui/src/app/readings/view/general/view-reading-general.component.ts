import { Component, OnInit } from '@angular/core';
import { NotificationService } from '../../../shared/notification.service';
import { Observable, of } from 'rxjs';
import { FormBuilder } from '@angular/forms';
import { MatDialog } from '@angular/material/dialog';
import { ActivatedRoute, Router } from '@angular/router';
import { ReadingModel } from '../../models/reading-model';
import { ReadingService } from '../../data/reading.service';

@Component({
    selector: 'robi-view-reading-general',
    templateUrl: './view-reading-general.component.html',
    styleUrls: ['./view-reading-general.component.css']
})
export class ViewReadingGeneralComponent implements OnInit {

    memberData: any;
    memberId = '';
    memberData$: any;

    profilePicUrl: string;
    profilePicFileToUpload: File = null;

    imageToShow: any;

    loader = false;
    memberShipForm = false;

    landlord$: Observable<any>;

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
}
