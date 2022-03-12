import { BaseDataSource } from '../../../shared/base-data-source';
import { PermissionSettingService } from './permission-setting.service';

export class PermissionSettingDataSource extends BaseDataSource {
    constructor(service: PermissionSettingService) {
        super(service);
    }
}
