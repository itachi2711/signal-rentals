import { BaseModel } from '../../shared/models/base-model';

export class BillingSummaryModel extends BaseModel {
    period_id: string;
    period_name: string;
    billed_amount: string;
    amount_due: string;
    amount_paid: string;
}
