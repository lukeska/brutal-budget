import { defineStore } from "pinia";

export const useProjectStore = defineStore("ProjectStore", {
    state: () => {
        return {
            sidebarOpen: false,
            project: {
                id: null,
                name: null,
                hex: "#fca5a5",
                permissions: { delete: true },
            },
        };
    },
    getters: {
        isNewProject: (state) => state.project.id == null,
        isSidebarOpen: (state) => state.sidebarOpen,
    },
    actions: {
        showSidebar(project) {
            if (project == null) {
                this.project = {
                    id: null,
                    name: null,
                    hex: "#fca5a5",
                    permissions: { delete: true },
                };
            } else {
                this.project = project;
            }
            this.sidebarOpen = true;
        },
        hideSidebar() {
            this.sidebarOpen = false;
        },
    },
});
