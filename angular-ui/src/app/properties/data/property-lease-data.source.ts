import { BaseDataSource } from '../../shared/base-data-source';
import { PropertyLeaseService } from './property-lease.service';

export class PropertyLeaseDataSource extends BaseDataSource {
    constructor(service: PropertyLeaseService) {
        super(service);
    }
}
