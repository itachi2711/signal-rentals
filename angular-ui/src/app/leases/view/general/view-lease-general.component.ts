import { Component, OnInit } from '@angular/core';
import { Observable } from 'rxjs';
import { ActivatedRoute } from '@angular/router';
import { LeaseService } from '../../data/lease.service';
import { LeaseModel } from '../../models/lease-model';

@Component({
    selector: 'robi-view-lease-general',
    templateUrl: './view-lease-general.component.html',
    styleUrls: ['./view-lease-general.component.css']
})
export class ViewLeaseGeneralComponent implements OnInit {

    lease$: Observable<LeaseModel>;

    id: string;
    leaseData: any;
    constructor(private leaseService: LeaseService, private route: ActivatedRoute) {}

    ngOnInit() {
        this.lease$ = this.leaseService.selectedLeaseChanges$;
        this.id = this.route.snapshot.paramMap.get('id');
        this.leaseService.selectedLeaseChanges$.subscribe(data => {
            this.leaseData = data;
        });
        if (this.leaseData == null) {
            this.lease$ = this.leaseService.getById(this.id);
        }
    }
}
