import { BaseModel } from '../../shared/models/base-model';

export class PropertyModel extends BaseModel {
    agent_id: string;
    property_code: string;
    landlord_id: string;
    landlord: any;
    property_name: string;
    property_photo: string;
    property_status: string;
    property_type_id: string;
    // @itachi Declared Variables
    plot_area: any;
    covered_area: any;
    dm_circle_rate: any;
    // @itachi Declared Variables
    location: string;
    latitude: string;
    longitude: string;
    address_1: string;
    address_2: string;
    country: string;
    state: string;
    city: string;
    zip: string;

    total_vacant_units: string;
    total_units: string;
    unit_total: string;
    property_type: any;

    extra_charges: any;
    units: any;
    late_fees: any;
    utility_costs: any;
    payment_methods: any;
    vacant_units: any;

    agent_commission_value: string;
    agent_commission_type: string;
}
