export const createCurrencyFormatter = (currency) => {
    const locale = navigator.language || "en-US";
    const formatter = new Intl.NumberFormat(locale, {
        style: "currency",
        currency,
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    const parseLocaleNumber = (value) => {
        if (typeof value !== "string") {
            return value;
        }

        const parts = formatter.formatToParts(12345.6); // Example number to identify separators
        const groupSeparator = parts.find(part => part.type === 'group')?.value || '';
        const decimalSeparator = parts.find(part => part.type === 'decimal')?.value || '.';

        // Replace group separators and normalize decimal separator
        const normalizedString = value
            .replace(new RegExp(`\\${groupSeparator}`, 'g'), '')
            .replace(new RegExp(`\\${decimalSeparator}`), '.');

        return parseFloat(normalizedString);
    };

    return {
        format: (value) => formatter.format(parseLocaleNumber(value)), // Format a number
        parse: (value) => parseLocaleNumber(value), // Parse a string
    };
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
