import { BaseModel } from '../../shared/models/base-model';

export class TerminateModel extends BaseModel {
    end_date: string;
    termination_reason: string;
}
