import { BaseModel } from '../../shared/models/base-model';

export class TenantModel extends BaseModel {
    agent_id: string;
    business_address: string;
    business_description: string;
    business_industry: string;
    business_name: string;
    city: string;
    confirmation_code: string;
    confirmed: boolean;
    phone: string;
    created_at: string;
    created_by: string;
    date_of_birth: string;
    deleted_by: string;
    email: string;
    emergency_contact_email: string;
    emergency_contact_name: string;
    emergency_contact_phone: string;
    emergency_contact_physical_address: string;
    emergency_contact_postal_address: string;
    emergency_contact_relationship: string;
    employer_contact_email: string;
    employer_contact_phone: string;
    employment_physical_address: string;
    employment_position: string;
    employment_postal_address: string;
    employment_status: string;
    first_name: string;
    middle_name: string;
    last_name: string;
    gender: string;
    id_passport_number: string;
    leases: [];
    marital_status: string;
    country: string;
    id_number: string;
    next_of_kin_name: string;
    next_of_kin_phone: string;
    next_of_kin_relation: string;
    password_set: boolean;
    physical_address: string;
    postal_address: string;
    postal_code: string;
    profile_pic: string;
    registration_number: string;
    rent_payment_contact: string;
    rent_payment_contact_physical_address: string;
    rent_payment_contact_postal_address: string;
    tenant_number: string;
    tenant_type_id: string;

    tenant_type: any;
    updated_by: string;

    password: string;
    password_confirmation: string;
}
