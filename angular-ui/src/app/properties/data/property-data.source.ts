import { PropertyService } from './property.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class PropertyDataSource extends BaseDataSource {
    constructor(service: PropertyService) {
        super(service);
    }
}
