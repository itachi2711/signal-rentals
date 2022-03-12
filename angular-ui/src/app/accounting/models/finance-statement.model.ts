import { BaseModel } from '../../shared/models/base-model';

export class FinanceStatementModel extends BaseModel {
    branch_id: string;
    start_date: string;
    end_date: string;
    statement_type_id: string;
}
