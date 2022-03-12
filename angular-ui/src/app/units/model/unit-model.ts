import { BaseModel } from '../../shared/models/base-model';

export class UnitModel extends BaseModel {
    agent_id: string;
    property_code: string;
    landlord_id: string;
    landlord: any;
    property_name: string;
    property_photo: string;
    property_status: string;
    property_type_id: string;
    location: string;
    latitude: string;
    longitude: string;
    address_1: string;
    address_2: string;
    country: string;
    state: string;
    city: string;
    zip: string;
    property_contact_first_name: string;
    property_contact_middle_name: string;
    property_contact_last_name: string;
    property_contact_phone: string;
    property_contact_email: string;

    total_vacant_units: string;
    total_units: string;
    unit_total: string;

    agent_commission_value: string;
    agent_commission_type: string;
}
