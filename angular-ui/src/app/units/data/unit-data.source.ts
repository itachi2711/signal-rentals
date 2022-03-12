import { UnitService } from './unit.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class UnitDataSource extends BaseDataSource {
    constructor(service: UnitService) {
        super(service);
    }
}
