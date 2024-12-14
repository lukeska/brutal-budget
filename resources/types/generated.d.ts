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
currency: App.Data.CurrencyData;
converted_amount: number;
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
export type CategoryTotalData = {
category_id: number;
total: number;
};
export type CurrencyData = {
id: number;
name: string;
code: string;
symbol: string;
};
export type ExpenseData = {
id?: number;
date: string;
amount: number;
currency: App.Data.CurrencyData;
converted_amount: number;
notes: string | null;
is_regular: boolean;
category: App.Data.CategoryData;
project: App.Data.ProjectData | null;
created_at: string;
user: App.Data.UserData;
permissions: { viewAny: boolean;view: boolean;create: boolean;update: boolean;delete: boolean;restore: boolean;forceDelete: boolean } };
export type ExpenseRequest = {
id?: number;
date: string;
amount: number;
currency_id: number;
notes: string | null;
is_regular: boolean;
category_id: number;
project_id: number | null;
months: number;
};
export type ExpensesIndexPage = {
expenses: Array<App.Data.ExpenseData>;
monthlyTotals: Array<App.Data.MonthlyTotalData>;
};
export type MonthlyTotalData = {
total: number;
yearMonth: number;
categoryMonthlyTotals: Array<App.Data.CategoryMonthlyTotalData>;
isCurrent: boolean;
};
export type OnboardingStatusData = {
id: number;
user_id: number;
onboarding_step: App.Enums.OnboardingSteps;
order: number;
skipped_at: any | null;
completed_at: any | null;
};
export type ProjectData = {
id?: number;
name: string;
hex: string;
permissions: { viewAny: boolean;view: boolean;create: boolean;update: boolean;delete: boolean;restore: boolean;forceDelete: boolean } };
export type ProjectTotalData = {
project_id: number;
total: number;
};
export type UserData = {
name: string;
currency: App.Data.CurrencyData | null;
};
export type UserSettingsRequest = {
currency_id: number;
};
}
declare namespace App.Enums {
export type OnboardingSteps = 'expense-created' | 'team-member-invited' | 'project-created';
}
