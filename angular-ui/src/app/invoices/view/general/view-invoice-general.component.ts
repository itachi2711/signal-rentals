import { Component, Input, OnInit } from '@angular/core';
import { NotificationService } from '../../../shared/notification.service';
import { Observable } from 'rxjs';

@Component({
    selector: 'robi-view-property-general',
    templateUrl: './view-invoice-general.component.html',
    styleUrls: ['./view-invoice-general.component.css']
})
export class ViewInvoiceGeneralComponent implements OnInit {

    memberData: any;
    memberId = '';
    memberData$: any;

    profilePicUrl: string;
    profilePicFileToUpload: File = null;

    imageToShow: any;

    loader = false;
    memberShipForm = false;

    landlord$: Observable<any>;

    constructor(private notification: NotificationService) {}

    ngOnInit() {
    }
}
