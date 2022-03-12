import { BaseModel } from '../../../../shared/models/base-model';

export class AmenityModel extends BaseModel {
    amenity_name: string;
    amenity_display_name: string;
    amenity_description: string;

    created_by: string;
    updated_by: string;
}

export function compare(a1: AmenityModel, a2: AmenityModel) {
    const xx  = a1.amenity_name < a2.amenity_name;

    if (xx === true) {
        return 1;
    }
    if (xx === false) {
        return-1;
    }

   // return  a1.updated_by?.localeCompare(a2.updated_by);
}
