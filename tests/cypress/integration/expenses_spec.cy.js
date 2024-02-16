describe("Expenses", () => {
    beforeEach(() => {
        cy.refreshDatabase();

        cy.login({
            attributes: {
                name: "luca",
            },
            state: ["withPersonalTeam"],
        });
    });

    it("can create an expense", () => {
        cy.visit("/expenses/2024/01");

        cy.get('[data-cy="expense-add-button"]').first().click();
        cy.get("#date").type("2024-01-01");
        cy.get("#amount").type("10.50");
        cy.get("#notes").type("Weekly groceries");

        cy.get('[data-cy="open-category-list-button"]').click();
        cy.get('[data-cy="select-category-button"]').contains("Food").click();

        cy.get('[data-cy="save-expense-button"]').click();

        cy.get('[data-cy="expand-category-expenses-button"]', { timeout: 10000 }).click();
        cy.contains("Weekly groceries").contains("10.50");
    });
});
