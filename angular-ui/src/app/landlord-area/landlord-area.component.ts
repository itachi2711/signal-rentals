import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { LandlordSummaryModel } from './model/landlord-summary-model';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Observable, of } from 'rxjs';
import { ChartType } from 'chart.js';


@Component({
    selector: 'robi-landlord-area',
    templateUrl: './landlord-area.component.html',
    styleUrls: ['./landlord-area.component.scss']
})
export class LandlordAreaComponent implements OnInit {

    periods$: Observable<any>;
    billingForm: FormGroup;
    landlordSummary: LandlordSummaryModel;

    billed$: Observable<string>;
    paid$: Observable<string>;
    pending$: Observable<string>;
    public pieChartLabels = ['Pending', 'Paid', 'Billed'];
    public pieChartData$: Observable<[any, any, any]>;
    public pieChartType = 'doughnut' as ChartType;
    options = {
        responsive: true,
        maintainAspectRatio: false
    }
    constructor(private fb: FormBuilder,
                private route: ActivatedRoute) {
    }

    ngOnInit() {
        if (this.route.snapshot.data['landlordData']) {
            this.landlordSummary = this.route.snapshot.data['landlordData'];
        }

        this.periods$ = of(this.landlordSummary?.periodical_billing);
        const firstPeriod = this.landlordSummary?.periodical_billing[0];
        this.billed$ = of(firstPeriod?.amount_billed_as_currency);
        this.paid$ = of(firstPeriod?.amount_paid_as_currency);
        this.pending$ = of(firstPeriod?.amount_due_as_currency);

        this.billingForm = this.fb.group({
            period_id: [firstPeriod?.period_id, [Validators.required]]
        });

        this.pieChartData$ = of([
            firstPeriod?.amount_due,
            firstPeriod?.amount_paid,
            firstPeriod?.amount_billed,
        ]);
    }

    onPeriodChange(periodID) {
        const periodBilling  = this.landlordSummary?.periodical_billing;
        const selectedPeriod = periodBilling.find((item: any) => item?.period_id === periodID);

        this.billed$ = of(selectedPeriod?.amount_billed_as_currency);
        this.paid$ = of(selectedPeriod?.amount_paid_as_currency);
        this.pending$ = of(selectedPeriod?.amount_due_as_currency);

        this.pieChartData$ = of([
            selectedPeriod?.amount_due,
            selectedPeriod?.amount_paid,
            selectedPeriod?.amount_billed,
        ]);
    }
}
