import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { TenantSummaryModel } from './model/tenant-summary-model';
import { ChartType } from 'chart.js';
import { Color } from 'ng2-charts';

@Component({
    selector: 'robi-landlord-area',
    templateUrl: './tenant-area.component.html',
    styleUrls: ['./tenant-area.component.scss']
})
export class TenantAreaComponent implements OnInit {
    tenantSummary: TenantSummaryModel;
    public lineChartLabels = [];
    public lineChartData = [];
    public lineChartType = 'line' as ChartType;
    options = {
        responsive: true,
        maintainAspectRatio: false
    }
    public lineChartLegend = true;
    public lineChartColors: Color[] = [
        {
            backgroundColor: ['rgba(0,255,0,0.3)', 'rgba(0,0,255,0.3)'],
        }
    ];
    constructor(private route: ActivatedRoute) {
    }
    ngOnInit() {

        if (this.route.snapshot.data['tenantData']) {
            this.tenantSummary = this.route.snapshot.data['tenantData'];
        }

        const paymentDate = [];
        const paidAmount = [];
        const periodBilling = this.tenantSummary?.payment_data.reverse();
        periodBilling.forEach(function (payment) {
            paymentDate.push(payment?.date);
            paidAmount.push(payment?.amount);
        });

        this.lineChartLabels = paymentDate;
        this.lineChartData = [
            {data: paidAmount, label: 'Payment'}
        ];
    }
}
