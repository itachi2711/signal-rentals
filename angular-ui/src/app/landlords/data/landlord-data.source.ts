import { LandlordService } from './landlord.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class LandlordDataSource extends BaseDataSource {
    constructor(service: LandlordService) {
        super(service);
    }
}
