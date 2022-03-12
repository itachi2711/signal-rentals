import { GeneralSettingService } from './general-setting.service';
import { BaseDataSource } from '../../../shared/base-data-source';

export class GeneralSettingDatasource extends BaseDataSource {
    constructor(service: GeneralSettingService) {
        super(service);
    }
}
