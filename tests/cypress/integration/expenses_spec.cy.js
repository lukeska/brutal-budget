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
        cy.visit("/expenses/2025/01");

        cy.get('[data-cy="expense-add-button"]').first().click();
        cy.get("#date").type("2025-01-01");
        cy.get("#amount").type("10.50");
        cy.get("#notes").type("Weekly groceries");

        cy.get('[data-cy="open-category-list-button"]').click();
        cy.get('[data-cy="select-category-button"]').contains("Food").click();

        cy.get('[data-cy="save-expense-button"]').click();

        cy.get('[data-cy="expand-category-expenses-button"]', { timeout: 10000 }).click();
        cy.contains("Weekly groceries", { timeout: 10000 });
        cy.contains("10.50");
    });

    it("can create a category from an expense creation form", () => {
        cy.visit("/expenses/2025/01");

        cy.get('[data-cy="expense-add-button"]').first().click();

        cy.get('[data-cy="open-category-list-button"]').click();

        cy.get('[data-cy="add-category-button"]').click();
        cy.contains("Add new category");

        cy.get('[data-cy="category-form"] #name').type("My Cat 123");
        cy.get('[data-cy="save-category-button"]').click();
        cy.contains("My Cat 123");
    });
});
