import { AmenityService } from './amenity.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class AmenityDataSource extends BaseDataSource {
    constructor(service: AmenityService) {
        super(service);
    }
}
