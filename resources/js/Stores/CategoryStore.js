import { defineStore } from "pinia";

const defaultCategory = {
    id: null,
    name: null,
    icon: "IconReceipt2",
    hex: "#fca5a5",
    permissions: { delete: true },
};

export const useCategoryStore = defineStore("CategoryStore", {
    state: () => {
        return {
            sidebarOpen: false,
            innerPanelOpen: false,
            category: defaultCategory,
        };
    },
    getters: {
        isNewCategory: (state) => state.category.id == null,
        isSidebarOpen: (state) => state.sidebarOpen,
        isInnerPanelOpen: (state) => state.innerPanelOpen,
    },
    actions: {
        showSidebar(category) {
            if (category == null) {
                this.category = defaultCategory;
            } else {
                this.category = category;
            }
            this.sidebarOpen = true;
        },
        hideSidebar() {
            this.sidebarOpen = false;
        },
        showInnerPanel(category) {
            if (category == null) {
                this.category = defaultCategory;
            } else {
                this.category = category;
            }
            this.innerPanelOpen = true;
        },
        hideInnerPanel() {
            this.innerPanelOpen = false;
        },
    },
});
