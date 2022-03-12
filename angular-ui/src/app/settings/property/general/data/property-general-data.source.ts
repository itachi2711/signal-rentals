import { PropertyGeneralService } from './property-general.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class PropertyGeneralDataSource extends BaseDataSource {
    constructor(service: PropertyGeneralService) {
        super(service);
    }
}
