declare namespace App.Data {
export type CategoryData = {
id?: number;
name: string;
icon: string;
hex: string;
permissions: { viewAny: boolean;view: boolean;create: boolean;update: boolean;delete: boolean;restore: boolean;forceDelete: boolean } };
export type CategoryMonthlyTotalData = {
id: number;
amount: number;
year_month: number;
category: App.Data.CategoryData;
};
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
categoryMonthlyTotals: Array<App.Data.CategoryMonthlyTotalData>;
categoryMonthlyTotalsPreviousMonth: Array<App.Data.CategoryMonthlyTotalData>;
categoryMonthlyTotalsFollowingMonth: Array<App.Data.CategoryMonthlyTotalData>;
totalExpenses: number;
totalExpensesPreviousMonth: number;
totalExpensesFollowingMonth: number;
year: number;
month: number;
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
