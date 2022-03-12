import { BaseModel } from '../../shared/models/base-model';

export class PaymentModel extends BaseModel {
    agent_id: string;
    lease_id: string;
    tenant_id: string;
    payment_method_id: string;
    currency_id: string;
    amount: string;
    payment_date: string;
    paid_by: string;

   payment_status: string;
   cancel_notes: string;
   cancelled_by: string;
   approved_by: string;

    status: any;
    is_pending: boolean;
    is_cancelled: boolean;
    is_approved: boolean;

    cancel_user: any;
    approve_user: any;
    lease: any;
    property: any;

    attachment: string;
    notes: string;
    receipt_number: string;
    payment_method: any;
    tenant: any;

    created_by: string;
    updated_by: string;
}
