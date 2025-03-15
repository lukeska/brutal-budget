import { defineConfig } from "cypress";
import plugins from "./tests/cypress/plugins/index.js";

/*
 * Fixed according to PR https://github.com/laracasts/cypress/pull/85
 */

export default defineConfig({
    projectId: "18opia",
    chromeWebSecurity: false,
    retries: 0,
    defaultCommandTimeout: 5000,
    watchForFileChanges: false,
    videosFolder: "tests/cypress/videos",
    screenshotsFolder: "tests/cypress/screenshots",
    fixturesFolder: "tests/cypress/fixture",
    e2e: {
        setupNodeEvents(on, config) {
            return plugins(on, config);
        },
        baseUrl: "https://brutal-budget-v11.test",
        specPattern: "tests/cypress/integration/**/*.cy.{js,jsx,ts,tsx}",
        supportFile: "tests/cypress/support/index.js",
    },
});
