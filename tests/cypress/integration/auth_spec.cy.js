describe("Authentication", () => {
    beforeEach(() => {
        cy.refreshDatabase();
    });

    it("provides feedback for invalid login credentials", () => {
        cy.visit("/login");

        cy.get("#email").type("foo@example.com");
        cy.get("#password").type("password");
        cy.contains("button", "Log in").click();

        cy.contains("These credentials do not match our records.");
    });

    it("allows users to login", () => {
        cy.create({
            model: "App\\Models\\User",
            attributes: { email: "luca@example.com" },
            state: ["withPersonalTeam"],
        });

        cy.visit("/login");

        cy.get("#email").type("luca@example.com");
        cy.get("#password").type("password");
        cy.contains("button", "Log in").click();

        cy.assertRedirect("/dashboard");
    });

    it("visits the dashboard", () => {
        cy.login({
            attributes: {
                name: "luca",
            },
            state: ["withPersonalTeam"],
        });
        cy.visit("/").contains("luca's Team");
    });
});
