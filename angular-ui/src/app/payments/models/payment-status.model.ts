import { BaseModel } from '../../shared/models/base-model';

export class PaymentStatusModel extends BaseModel {
    id: string;
    payment_status: string;
    cancel_notes: string;
}
