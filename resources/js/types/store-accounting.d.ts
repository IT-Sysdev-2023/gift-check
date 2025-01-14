export interface gcMonthlyTypes {
    dataTypeMonthly: string;
    year: string;
    month: string;
    selectedStore: string;
    errors: { [key: number | string]: string };
}

export interface gcYearlyType {
    GCDataType: string;
    selectedStore: string;
    year: string;
    errors: { [key: number | string]: string };
}