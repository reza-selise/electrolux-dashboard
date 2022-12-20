import { Select } from 'antd';
import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setEventbyYearTimelineMonth } from '../../Redux/Slice/EventByYear/eventByYearTimelineMonth';
import { setEventbyYearTimelineYears } from '../../Redux/Slice/EventByYear/eventByYearTimelineYear';
import './FilterTypeYear.scss';

function FilterTypeYear() {
    const eventbyYearTimelineYears = useSelector(state => state.eventbyYearTimelineYears.value);
    const location = useSelector(state => state.location.value);
    const [years, setYears] = useState([]);
    const [needMonth, setNeedMonth] = useState(false);

    const currentYear = new Date().getFullYear();

    const dispatch = useDispatch();

    const handleYearChange = value => {
        switch (location) {
            case 'event-by-year-timeline':
                dispatch(setEventbyYearTimelineYears(value));
                break;
            case 'event-by-category-timeline':
                dispatch(setEventbyYearTimelineYears(value));
                break;

            default:
                console.log('filter type year default');
        }
    };

    const handleNeedMonth = () => {
        setNeedMonth(!needMonth);
    };

    const handleMonthChange = value => {
        switch (location) {
            case 'event-by-year-timeline':
                dispatch(setEventbyYearTimelineMonth(value));
                break;

            default:
                console.log('filter type year month default');
        }
    };

    useEffect(() => {
        const years = [];
        for (let year = 1950; year <= currentYear; year += 1) {
            const yearObject = {
                label: year,
                value: year,
            };

            years.push(yearObject);
            setYears(years);
        }
    }, []);

    const monthOptions = [
        {
            label: 'January',
            value: 1,
        },
        {
            label: 'February',
            value: 2,
        },
        {
            label: 'March',
            value: 3,
        },
        {
            label: 'April',
            value: 4,
        },
        {
            label: 'May',
            value: 5,
        },
        {
            label: 'June',
            value: 6,
        },
        {
            label: 'July',
            value: 7,
        },
        {
            label: 'August',
            value: 8,
        },
        {
            label: 'September',
            value: 9,
        },
        {
            label: 'October',
            value: 10,
        },
        {
            label: 'November',
            value: 11,
        },
        {
            label: 'December',
            value: 12,
        },
    ];
    return (
        <>
            <div className="filter-type-year">
                <Select
                    placeholder="Please enter years"
                    style={{
                        width: '100%',
                    }}
                    mode="multiple"
                    onChange={handleYearChange}
                    options={years}
                />
            </div>

            {eventbyYearTimelineYears !== '' && (
                <label htmlFor="need-month">
                    <input type="checkbox" onChange={handleNeedMonth} id="need-month" />
                    Need Months
                </label>
            )}
            {needMonth && (
                <Select
                    placeholder="Please enter months"
                    style={{
                        width: '100%',
                    }}
                    mode="multiple"
                    onChange={handleMonthChange}
                    options={monthOptions}
                    className="filter-type-month"
                />
            )}
        </>
    );
}

export default FilterTypeYear;
