import { BaseModel } from '../../shared/models/base-model';

export class UserProfileModel extends BaseModel {
    role: any;
    first_name: string;
    middle_name: string;
    last_name: string;
    email: string;
    phone: string;
    country: string;
    city: string;
    state: string;
    physical_address: string;
    postal_address: string;
    postal_code: string;
    current_password: string;
    password: string;
    password_confirmation: string;
}
