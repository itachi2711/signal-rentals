import { RoleSettingService } from './role-setting.service';
import { BaseDataSource } from '../../../shared/base-data-source';

export class RoleSettingDataSource extends BaseDataSource {
    constructor(service: RoleSettingService) {
        super(service);
    }
}
