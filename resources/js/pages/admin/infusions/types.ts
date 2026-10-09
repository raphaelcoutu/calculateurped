export type Preparation = {
    min_weight: number;
    max_weight: number | null;
    concentration: number;
    concentration_unit: string;
    total_volume: number;
    instructions: string;
    position: number;
};

export type Recipe = {
    id: number;
    name: string;
    brand_name: string;
    concentration: string;
    debit_min: number;
    debit_max: number;
    dose_unit: string;
    dose_per_kg: boolean;
    debit_dose_unit: string;
    debit_time_unit: string;
    debit_min_limit: number;
    debit_max_limit: number;
    debit_limit_unit: string;
    dosage_precision: number;
    type: number;
    order: number;
    preparations: Preparation[];
};

export type Gap = { min: number; max: number };
