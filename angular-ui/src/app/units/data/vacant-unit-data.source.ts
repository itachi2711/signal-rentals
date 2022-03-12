import { BaseDataSource } from '../../shared/base-data-source';
import { VacantUnitService } from './vacant-unit.service';

export class VacantUnitDataSource extends BaseDataSource {
    constructor(service: VacantUnitService) {
        super(service);
    }
}
