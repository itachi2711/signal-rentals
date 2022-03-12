import { VacateService } from './vacate.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class VacateDataSource extends BaseDataSource {
    constructor(service: VacateService) {
        super(service);
    }
}
