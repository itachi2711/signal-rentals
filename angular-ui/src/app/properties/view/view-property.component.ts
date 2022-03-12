import { AfterViewInit, Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { PropertyModel } from '../models/property-model';
import { MatDialog, MatDialogConfig } from '@angular/material/dialog';
import { AddPropertyComponent } from '../add/add-property.component';
import { PropertyService } from '../data/property.service';

import {
    IBarChartOptions,
    IChartistAnimationOptions,
    IChartistData
} from 'chartist';
import { ChartEvent, ChartType } from 'ng-chartist';
import { select, Store } from '@ngrx/store';
import { AppState } from '../../reducers';
import { selectorIsLandlord, selectorUserID } from '../../authentication/authentication.selectors';
import { LandlordService } from '../../landlords/data/landlord.service';
import { AuthenticationService } from '../../authentication/authentication.service';

@Component({
    selector: 'robi-view-property',
    styleUrls: ['./view-property.component.scss'],
    templateUrl: './view-property.component.html'
})
export class ViewPropertyComponent implements OnInit, AfterViewInit  {

    id: string;

    type: ChartType = 'Bar';
    type2: ChartType = 'Line';
    type3: ChartType = 'Pie';
    data: IChartistData = {
        labels: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ],
        series: [
            [5, 4, 3, 7, 5, 10, 3, 4, 8, 10, 6, 8],
            [3, 2, 9, 5, 4, 6, 4, 6, 7, 8, 7, 4]
        ]
    };

    options: IBarChartOptions = {
        axisX: {
            showGrid: true
        },
        height: 250
    };

    events: ChartEvent = {
        draw: (data) => {
            if (data.type === 'bar') {
                data.element.animate({
                    y2: <IChartistAnimationOptions>{
                        dur: '0.5s',
                        from: data.y1,
                        to: data.y2,
                        easing: 'easeOutQuad'
                    }
                });
            }
        }
    };

    saleData = [
        { name: 'Jan', value: 105000 },
        { name: 'Feb', value: 55000 },
        { name: 'March', value: 15000 },
        { name: 'April', value: 150000 },
        { name: 'May', value: 20000 },
        { name: 'June', value: 20000 },
        { name: 'July', value: 20000 },
        { name: 'August', value: 20000 },
        { name: 'September', value: 20000 },
        { name: 'October', value: 20000 },
        { name: 'November', value: 20000 },
        { name: 'December', value: 20000 }
    ];

  /*  barChartOptions: ChartOptions = {
        responsive: true,
    };
    barChartLabels: Label[] = ['Jan', 'Feb', 'March', 'April', 'May', 'June'];
    barChartType: ChartType = 'bar';
    barChartLegend = true;
    barChartPlugins = [];*/

  /*  barChartData: ChartDataSets[] = [
        { data: [105000, 55000, 252000, 200000, 150000, 100000, 100000, 580000, 100000, 560000, 100000, 100000], label: 'Best Fruits' }
    ];*/

   /* public barChartData: ChartDataSets[] = [
        { data: [65000, 59000, 80000, 81000, 56000, 55000 ], label: 'Rent Paid' },
        { data: [28000, 48000, 40000, 19000, 86000, 27000], label: 'Late Rent' }
    ];

    public barChartColors: Color[] = [
        { backgroundColor: 'green' },
        { backgroundColor: 'red' },
    ]*/
    isLandlord = false;
    landlordID: string;

    propertyID: string;
    property$: Observable<any>;
    propertyData$: Observable<PropertyModel>;
    isAdmin$: Observable<boolean>;
    constructor(private store: Store<AppState>,
                private dialog: MatDialog,
                private landlordService: LandlordService,
                private propertyService: PropertyService,
                private authenticationService: AuthenticationService,
                private router: Router, private route: ActivatedRoute) {
        this.isAdmin$ = this.authenticationService.isAdmin();
        this.store.pipe(select(selectorIsLandlord)).subscribe(isLandlord => {
            if (isLandlord) {
                this.isLandlord = true;
                this.store.pipe(select(selectorUserID)).subscribe(userID => this.landlordID = userID);
            }
        });
    }

    ngOnInit() {
        this.propertyID = this.route.snapshot.paramMap.get('id');
        this.propertyService.selectedPropertyChanges$.subscribe(data => {
            if (data) {
                this.propertyData$ = of(data);
            }
            if (!data) {
                if (this.isLandlord) {
                    this.landlordService.getNestedById(this.landlordService.nestedPropertyUrl(this.landlordID, this.propertyID))
                        .subscribe(property => {
                        this.propertyData$ = of(property);
                        this.propertyService.changeSelectedProperty(property);
                    });
                } else {
                    this.propertyService.getById(this.propertyID).subscribe(property => {
                        this.propertyData$ = of(property);
                        this.propertyService.changeSelectedProperty(property);
                    });
                }
            }
        });
    }

    /**
     * Add dialog launch
     */
    addDialog(mode: string, property?: PropertyModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {property,
            mode: mode
        };

        const dialogRef = this.dialog.open(AddPropertyComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    // this.loadData();
                }
            }
        );
    }

    ngAfterViewInit(): void {}

    onSelected(property: PropertyModel) {
        this.propertyService.changeSelectedProperty(property);
    }
}
