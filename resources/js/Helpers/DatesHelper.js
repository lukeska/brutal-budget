import moment from "moment";

export const getDate = (yearMonth) => {
    return moment(yearMonth, "YYYYMM");
};
