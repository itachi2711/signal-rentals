import { BaseModel } from '../../shared/models/base-model';
import { StatementModel } from './statement.model';

export class AccountingModel extends BaseModel {
    data: any;
    branch_id: string;
    account_number: string;
    account_code: string;
    account_name: string; // Will be like member_id in some
    account_display_name: string;
    account_type_id: string;
    account_status_id: string;
    other_details: string;
    closed_on: string;

    created_by: string;
    updated_by: string;
    deleted_by: string;

    accountClass: any;
    accountType: any;
    accountBalance: any;
    member: any;
    statement: StatementModel;
}
