import { Select } from 'antd';
import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setCookingCourseMonths } from '../../Redux/Slice/CookingCourseType/CookingCourseMonths';
import { setEventbyMonthTimelineMonth } from '../../Redux/Slice/EventByMonth/eventMyMonthTimelineMonth';
import { setEventbyYearTimelineMonth } from '../../Redux/Slice/EventByYear/eventByYearTimelineMonth';
import { setEventByStatusMonths } from '../../Redux/Slice/EventByStatus/EventByStatusMonths';

function FilterTypeMonth() {
    const location = useSelector(state => state.location.value);
    const dispatch = useDispatch();

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
    const filterTypeMonthHandle = value => {
        switch (location) {
            case 'event-by-year-timeline':
                dispatch(setEventbyYearTimelineMonth(value));
                break;
            case 'event-by-months-timeline':
                dispatch(setEventbyMonthTimelineMonth(value));
                break;
            case 'cooking-course-type-timeline':
                dispatch(setCookingCourseMonths(value));
                break;
            case 'event-by-status-timeline':
                dispatch(setEventByStatusMonths(value));
                break;

            default:
                console.log('filter type month default');
        }
    };
    return (
        <Select
            placeholder="Please enter months"
            style={{
                width: '100%',
            }}
            mode="multiple"
            onChange={filterTypeMonthHandle}
            options={monthOptions}
            className="filter-type-month"
        />
    );
}

export default FilterTypeMonth;
