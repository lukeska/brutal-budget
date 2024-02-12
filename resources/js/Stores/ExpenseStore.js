import { defineStore } from "pinia";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

function getCurrentDateFormatted() {
    const currentDate = new Date();
    const day = currentDate.getDate().toString().padStart(2, "0");
    const month = (currentDate.getMonth() + 1).toString().padStart(2, "0"); // Note: Months are zero-based
    const year = currentDate.getFullYear();

    return `${day}-${month}-${year}`;
}

export const useExpenseStore = defineStore("ExpenseStore", {
    state: () => {
        return {
            sidebarOpen: false,
            expense: null,
        };
    },
    getters: {
        isNewExpense: (state) => state.expense.id == null,
    },
    actions: {
        showSidebar(expense, category = null) {
            if (expense == null) {
                this.expense = {
                    id: null,
                    amount: null,
                    date: getCurrentDateFormatted(),
                    notes: null,
                    category: category ? category : page.props.categories[0],
                    is_regular: true,
                };
            } else {
                this.expense = expense;
            }
            this.sidebarOpen = true;
        },
        hideSidebar() {
            this.sidebarOpen = false;
        },
    },
});
