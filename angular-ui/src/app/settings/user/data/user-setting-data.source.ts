import { UserSettingService } from './user-setting.service';
import { BaseDataSource } from '../../../shared/base-data-source';

export class UserSettingDataSource extends BaseDataSource {
    constructor(service: UserSettingService) {
        super(service);
    }
}
