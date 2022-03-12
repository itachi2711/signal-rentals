import { LeaseSettingService } from './lease-setting.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class LeaseGeneralDataSource extends BaseDataSource {
    constructor(service: LeaseSettingService) {
        super(service);
    }
}
