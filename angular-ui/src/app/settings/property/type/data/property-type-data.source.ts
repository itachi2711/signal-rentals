import { PropertyTypeService } from './property-type.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class PropertyTypeDataSource extends BaseDataSource {
    constructor(service: PropertyTypeService) {
        super(service);
    }
}
