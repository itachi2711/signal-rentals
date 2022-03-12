import { UtilityService } from './utility.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class UtilityDataSource extends BaseDataSource {
    constructor(service: UtilityService) {
        super(service);
    }
}
