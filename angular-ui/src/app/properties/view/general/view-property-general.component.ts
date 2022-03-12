import { Component, OnInit } from '@angular/core';
import { Observable } from 'rxjs';
import { PropertyService } from '../../data/property.service';
import { PropertyModel } from '../../models/property-model';
import { ActivatedRoute } from '@angular/router';

@Component({
    selector: 'robi-view-property-general',
    templateUrl: './view-property-general.component.html',
    styleUrls: ['./view-property-general.component.css']
})
export class ViewPropertyGeneralComponent implements OnInit {

    memberData: any;
    memberId = '';
    memberData$: any;

    profilePicUrl: string;
    profilePicFileToUpload: File = null;

    imageToShow: any;

    loader = false;
    memberShipForm = false;

    property$: Observable<PropertyModel>;

    id: string;
    propertyData: any;

    constructor(private propertyService: PropertyService, private route: ActivatedRoute) {
    }

    ngOnInit() {
        this.property$ = this.propertyService.selectedPropertyChanges$;

        this.id = this.route.snapshot.paramMap.get('id');

        this.propertyService.selectedPropertyChanges$.subscribe(data => {
            this.propertyData = data;
        });

        if (this.propertyData == null) {
            this.property$ = this.propertyService.getById(this.id);
        }
    }
}
