import { defineStore } from "pinia";

export const useCategoryStore = defineStore("CategoryStore", {
    state: () => {
        return {
            sidebarOpen: false,
            category: null,
        };
    },
    getters: {
        isNewCategory: (state) => state.category.id == null,
    },
    actions: {
        showSidebar(category) {
            if (category == null) {
                this.category = {
                    id: null,
                    name: null,
                    icon: "IconReceipt2",
                    hex: "#fca5a5",
                    permissions: { delete: true },
                };
            } else {
                this.category = category;
            }
            this.sidebarOpen = true;
        },
        hideSidebar() {
            this.sidebarOpen = false;
        },
    },
});
