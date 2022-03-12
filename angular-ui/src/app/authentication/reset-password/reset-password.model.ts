import { BaseModel } from '../../shared/models/base-model';

export class ResetPasswordModel extends BaseModel {
    email: string;
    token: string;
    password: string;
    password_confirmation: string;
}
