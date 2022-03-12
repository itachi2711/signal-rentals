import { BaseModel } from '../../shared/models/base-model';

export class LandlordModel extends BaseModel {
    agent_id: string;
    agent: {};
    assets: string;
    city: string;
    country: string;
    county: string;
    created_at: string;
    created_by: string;
    registration_date: string;
    registration_date_display: string;
    date_of_birth: string;
    date_of_birth_display: string;
    email: string;
    extra_images: string;
    first_name: string;
    guaranteedLoans: string;
    id: string;
    id_number: string;
    last_name: string;
    middle_name: string;
    passport_number: string;
    passport_photo: string;
    phone: string;
    physical_address: string;
    residential_address: string;
    postal_address: string;
    profile_pic: string;
    property_total: number;
    state: string;
    status_id: string;
    unit_total: number;
    updated_at: string;
    updated_by: string;

    password: string;
    password_confirmation: string;
}
