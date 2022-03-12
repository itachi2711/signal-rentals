import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { MatDialog } from '@angular/material/dialog';
import { DomSanitizer } from '@angular/platform-browser';
import { select, Store } from '@ngrx/store';
import { selectorIsLandlord, selectorUserID } from '../../authentication/authentication.selectors';
import { AppState } from '../../reducers';
import { LandlordService } from '../../landlords/data/landlord.service';
import { PaymentModel } from '../models/payment-model';
import { PaymentService } from '../data/payment.service';

@Component({
    selector: 'robi-view-payment',
    styleUrls: ['./view-payment.component.scss'],
    templateUrl: './view-payment.component.html'
})
export class ViewPaymentComponent implements OnInit, AfterViewInit  {

    form: FormGroup;
    generalForm: FormGroup;
    guarantorForm: FormGroup;
    assetForm: FormGroup;

    formErrors: any;

    loader = false;

    memberStatuses: any = [];
    members: any = [];
    guarantorStatues: any = [];

    paymentID: string;

    routeData: any;

    memberData: any;
    memberId = '';
    memberData$: any;

    imageToShow: any;

    landlord$: Observable<any>;

    paymentData$: Observable<PaymentModel>;
    public src: Blob;

    pdfSrc: any;
    domSanitizer: DomSanitizer;

    isLandlord = false;
    landlordID: string;
    constructor(private store: Store<AppState>,
                private landlordService: LandlordService,
                private fb: FormBuilder,
                sanitizer: DomSanitizer,
                private dialog: MatDialog,
                private paymentService: PaymentService,
                private notification: NotificationService,
                private router: Router, private route: ActivatedRoute) {
        this.domSanitizer = sanitizer;

        this.store.pipe(select(selectorIsLandlord)).subscribe(isLandlord => {
            if (isLandlord) {
                this.isLandlord = true;
                this.store.pipe(select(selectorUserID)).subscribe(userID => this.landlordID = userID);
            }
        });

    }

    ngOnInit() {
        this.paymentID = this.route.snapshot.paramMap.get('id');
        this.paymentData$ = this.paymentService.selectedPaymentChanges$;

        this.paymentService.selectedPaymentChanges$.subscribe(data => {
            if (data) {
                this.paymentData$ = of(data);
            }
            if (!data) {
               // this.paymentData$ = this.paymentService.getById(this.paymentID);

                if (this.isLandlord) {
                    this.landlordService.getNestedById(this.landlordService.nestedInvoiceUrl(this.landlordID, this.paymentID))
                        .subscribe(payment => {
                            this.paymentData$ = of(payment);
                            this.paymentService.changeSelectedPayment(payment);
                        });
                } else {
                    this.paymentService.getById(this.paymentID).subscribe(payment => {
                        this.paymentData$ = of(payment);
                        this.paymentService.changeSelectedPayment(payment);
                    });
                }
            }
        });
        this.downloadInvoice(this.paymentID)
    }

    /**
     * @param paymentID
     */
    downloadInvoice(paymentID: string) {
        this.loader = true;

        this.paymentService.downloadReceipt({id: paymentID, pdf: true})
            .subscribe((res) => {
                    this.pdfSrc = this.domSanitizer.bypassSecurityTrustResourceUrl(
                        URL.createObjectURL(res)
                    );
                    this.loader = false;
                },
                () => {
                    this.loader = false;
                    this.notification.showNotification('danger', 'Error downloading receipt !');
                }
            );
    }

    /**
     *
     * @param blob
     */
    showFile(blob) {
        const newBlob = new Blob([blob], {type: 'application/pdf'});

        if (window.navigator && window.navigator.msSaveOrOpenBlob) {
            window.navigator.msSaveOrOpenBlob(newBlob);
            return;
        }
        const data = window.URL.createObjectURL(newBlob);
        const link = document.createElement('a');
        link.href = data;
        link.download = 'statement.pdf';
        link.click();
        setTimeout(function() {
            window.URL.revokeObjectURL(data);
        }, 100);
    }

    onOutletActivated(componentReference) {
    }

    ngAfterViewInit(): void {}

}
