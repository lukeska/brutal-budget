export const createCurrencyFormatter = (currency) => {
    const locale = navigator.language || "en-US";
    return new Intl.NumberFormat(locale, {
        style: "currency",
        currency,
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

export const getCurrencySymbol = (currencyCode) => {
    try {
        const formatter = new Intl.NumberFormat("en-US", { style: "currency", currency: currencyCode });
        const parts = formatter.formatToParts(1);
        let currencySymbol = "";

        for (const part of parts) {
            if (part.type === "currency") {
                currencySymbol = part.value;
                break;
            }
        }

        return currencySymbol;
    } catch (error) {
        console.error(`Error getting currency symbol for ${currencyCode}: ${error.message}`);
        return null;
    }
};
