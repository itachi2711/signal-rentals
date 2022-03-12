import { BaseModel } from '../../shared/models/base-model';

export class ReadingModel extends BaseModel {
    agent_id: string;
    property_id: string;
    unit_id: string;

    utility_id: string;

    base_charge: string;
    previous_reading: string;
    current_reading: string;
    reading_date: string;
    rate_per_unit: string;
    units: string;
    total: string;

    utility: any;
    property: any;
    unit: any;

    created_by: string;
    updated_by: string;
}
