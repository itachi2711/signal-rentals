import { BaseModel } from '../../shared/models/base-model';

export class TenantSummaryModel extends BaseModel {
    pending_amount: string;
    total_leases: string;
    leases: any;
    payment_data: any;
    amount_due: string;
    amount_paid: string;
}
