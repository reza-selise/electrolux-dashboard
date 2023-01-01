import { DatePicker } from 'antd';
// eslint-disable-next-line import/no-extraneous-dependencies
import moment from 'moment';
import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import deleteIcon from '../../images/delete.svg';
import plusIcon from '../../images/plus.svg';
import { setCookingCourseCustomDate } from '../../Redux/Slice/CookingCourseType/CookingCourseCustomDate';
import { setEventByCancellationCustomDate } from '../../Redux/Slice/EventByCancellation/EventByCancellationCustomDate';
import { setEventByStatusCustomDate } from '../../Redux/Slice/EventByStatus/EventByStatusCustomDate';
import { setEventbyYearTimelineYearDateRange } from '../../Redux/Slice/EventByYear/eventbyYearTimelineYearDateRange';
import './FilterTypeCustomDate.scss';

function FilterTypeCustomDate() {
    const location = useSelector(state => state.location.value);

    const [dateRanges, setDateRanges] = useState([{ start: null, end: null }]);
    const assetsPath = window.eluxDashboard.assetsUrl;
    const dispatch = useDispatch();

    const addDateRange = () => {
        setDateRanges([...dateRanges, { start: null, end: null }]);
    };

    const deleteDateRange = index => {
        setDateRanges(dateRanges.filter((_, i) => i !== index));
    };

    const handleFromChange = (date, index) => {
        // const newDateRanges = [...dateRanges];
        // newDateRanges[index].start = moment(date).format('MMDDYYYY');
        // setDateRanges(newDateRanges);
        const newDateRanges = [...dateRanges];
        newDateRanges[index] = {
            ...newDateRanges[index],
            start: moment(date).format('YYYYMMDD'),
        };
        setDateRanges(newDateRanges);
    };

    const handleToChange = (date, index) => {
        // const newDateRanges = [...dateRanges];
        // newDateRanges[index].end = moment(date).format('MMDDYYYY');
        // setDateRanges(newDateRanges);
        const newDateRanges = [...dateRanges];
        newDateRanges[index] = {
            ...newDateRanges[index],
            end: moment(date).format('YYYYMMDD'),
        };
        setDateRanges(newDateRanges);
    };

    useEffect(() => {
        switch (location) {
            case 'event-by-year-timeline':
                dispatch(setEventbyYearTimelineYearDateRange(dateRanges));
                break;
            case 'event-by-location-timeline':
                // dispatch(seteventbyLocationimelineYearDateRange(dateRanges));
                break;
            case 'event-by-months-timeline':
                // dispatch(setEventbyMonthTimelineYears([...new Set(yearsArray)]));
                // dispatch(setEventbyMonthTimelineMonth([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]));
                break;

            case 'cooking-course-type-timeline':
                dispatch(
                    setCookingCourseCustomDate(dateRanges.map(({ start, end }) => [start, end]))
                );
                break;
            case 'event-by-status-timeline':
                dispatch(setEventByStatusCustomDate(dateRanges));
                break;

            case 'event-by-cancellation-timeline':
                dispatch(setEventByCancellationCustomDate(dateRanges));
                break;

            default:
                console.log('filter type year month default', location);
        }
    }, [location, dateRanges]);

    return (
        <div className="custom-date-range-wrapper">
            {dateRanges.map((dateRange, index) => (
                <div key={index}>
                    <div key={index} className="el-custom-date-picker">
                        <DatePicker
                            placeholder="MMM D, YYYY"
                            onChange={date => handleFromChange(date, index)}
                            format="MMM D, YYYY"
                        />
                        <span className="from-to-title">To</span>
                        <DatePicker
                            placeholder="MMM D, YYYY"
                            onChange={date => handleToChange(date, index)}
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
                </div>
            ))}
            <button className="el-add-another-btn" type="button" onClick={addDateRange}>
                Add another <img src={assetsPath + plusIcon} alt="plus icon" />
            </button>
        </div>
    );
}

export default FilterTypeCustomDate;
