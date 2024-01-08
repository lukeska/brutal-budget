declare namespace App.Data {
export type CategoryData = {
id?: number;
name: string;
icon: string;
hex: string;
};
export type CategoryMonthlyTotalData = {
id: number;
amount: number;
year_month: number;
category: App.Data.CategoryData;
previous_month_delta_amount: number | null;
};
export type CategoryRequest = {
id?: number;
name: string;
icon: string;
hex: string;
permissions: { viewAny: boolean;view: boolean;create: boolean;update: boolean;delete: boolean;restore: boolean;forceDelete: boolean } };
export type ExpenseData = {
id?: number;
date: string;
amount: number;
notes: string | null;
is_regular: boolean;
category: App.Data.CategoryData;
project: App.Data.ProjectData | null;
};
export type ExpenseRequest = {
id?: number;
date: string;
amount: number;
notes: string | null;
is_regular: boolean;
category_id: number;
project_id: number | null;
};
export type ExpensesIndexPage = {
expenses: Array<App.Data.ExpenseData>;
monthlyTotals: Array<App.Data.MonthlyTotalData>;
expensesView: string;
};
export type MonthlyTotalData = {
total: number;
yearMonth: number;
categoryMonthlyTotals: Array<App.Data.CategoryMonthlyTotalData>;
isCurrent: boolean;
};
export type ProjectData = {
id?: number;
name: string;
hex: string;
permissions: { viewAny: boolean;view: boolean;create: boolean;update: boolean;delete: boolean;restore: boolean;forceDelete: boolean } };
export type UserSettingsRequest = {
currency: string;
};
}
