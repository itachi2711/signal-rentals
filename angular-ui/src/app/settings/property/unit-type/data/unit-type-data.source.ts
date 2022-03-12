import { UnitTypeService } from './unit-type.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class UnitTypeDataSource extends BaseDataSource {
    constructor(service: UnitTypeService) {
        super(service);
    }
}
