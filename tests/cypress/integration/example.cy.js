describe("Example Test", () => {
    it("shows a login page", () => {
        cy.visit("/login");

        cy.contains("Log in");
    });
});
