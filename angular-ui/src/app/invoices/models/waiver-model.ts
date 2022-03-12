import { BaseModel } from '../../shared/models/base-model';

export class WaiverModel extends BaseModel {
    end_date: string;
    termination_reason: string;
}
