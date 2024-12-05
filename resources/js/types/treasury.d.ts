import type { UploadFile } from "ant-design-vue";

export interface ReportsGeneratedTypes {
    files: {
        file: string;
        filename: string;
        extension: string;
        expiration: string;
    }[];
}

export interface BudgetRequestForm {
    file: UploadFile | null;
    br: string;
    budget: number;
    remarks: string;
    dateNeeded: null;
    category: string;
}

export interface BudgetRequestProps {
    title?: string;
    remainingBudget: string;
    regularBudget: string;
    specialBudget: string;
    br: string;
}
