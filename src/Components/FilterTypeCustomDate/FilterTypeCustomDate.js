import { DatePicker } from 'antd';
import React, { useState } from 'react';
import deleteIcon from '../../images/delete.svg';
import plusIcon from '../../images/plus.svg';
import './FilterTypeCustomDate.scss';

function FilterTypeCustomDate() {
    const [dateRanges, setDateRanges] = useState([{ from: null, to: null }]);
    const assetsPath = window.eluxDashboard.assetsUrl;

    const addDateRange = () => {
        setDateRanges([...dateRanges, { from: null, to: null }]);
    };

    const deleteDateRange = (index) => {
        setDateRanges(dateRanges.filter((_, i) => i !== index));
    };

    const handleFromChange = (date, index) => {
        const newDateRanges = [...dateRanges];
        newDateRanges[index].from = date;
        setDateRanges(newDateRanges);
    };

    const handleToChange = (date, index) => {
        const newDateRanges = [...dateRanges];
        newDateRanges[index].to = date;
        setDateRanges(newDateRanges);
    };
    const commonSelectedYears = () => {
        const selectedYears = dateRanges.map((dateRange) => {
            const fromYear = dateRange.from ? dateRange.from.getFullYear() : null;
            const toYear = dateRange.to ? dateRange.to.getFullYear() : null;
            return [fromYear, toYear];
        });

        const commonYears = selectedYears.reduce((acc, years) => {
            const [fromYear, toYear] = years;
            if (fromYear && toYear) {
                for (let year = fromYear; year <= toYear; year += 1) {
                    acc[year] = (acc[year] || 0) + 1;
                }
            } else if (fromYear) {
                acc[fromYear] = (acc[fromYear] || 0) + 1;
            } else if (toYear) {
                acc[toYear] = (acc[toYear] || 0) + 1;
            }
            return acc;
        }, {});

        return Object.keys(commonYears).filter((year) => commonYears[year] === dateRanges.length);
    };
    const commonSelectedMonths = () => {
        const selectedMonths = dateRanges.map((dateRange) => {
            const fromMonth = dateRange.from ? dateRange.from.getMonth() : null;
            const toMonth = dateRange.to ? dateRange.to.getMonth() : null;
            return [fromMonth, toMonth];
        });

        const commonMonths = selectedMonths.reduce((acc, months) => {
            const [fromMonth, toMonth] = months;
            if (fromMonth && toMonth) {
                for (let month = fromMonth; month <= toMonth; month += 1) {
                    acc[month] = (acc[month] || 0) + 1;
                }
            } else if (fromMonth) {
                acc[fromMonth] = (acc[fromMonth] || 0) + 1;
            } else if (toMonth) {
                acc[toMonth] = (acc[toMonth] || 0) + 1;
            }
            return acc;
        }, {});

        return Object.keys(commonMonths).filter(
            (month) => commonMonths[month] === dateRanges.length
        );
    };

    return (
        <div className="custom-date-range-wrapper">
            {dateRanges.map((dateRange, index) => (
                <>
                    <div key={index} className="el-custom-date-picker">
                        <DatePicker
                            placeholder="From"
                            onChange={(date) => handleFromChange(date, index)}
                            format="MMM D, YYYY"
                        />
                        <span className="from-to-title">To</span>
                        <DatePicker
                            placeholder="To"
                            onChange={(date) => handleToChange(date, index)}
                            format="MMM D, YYYY"
                        />
                    </div>
                    {index < dateRanges.length - 1 && (
                        <button
                            className="el-delete-btn"
                            type="button"
                            onClick={() => deleteDateRange(index)}
                        >
                            <img src={assetsPath + deleteIcon} alt="delete icon" /> Remove
                        </button>
                    )}
                </>
            ))}
            <button className="el-add-another-btn" type="button" onClick={addDateRange}>
                Add another <img src={assetsPath + plusIcon} alt="plus icon" />
            </button>
        </div>
    );
}

export default FilterTypeCustomDate;