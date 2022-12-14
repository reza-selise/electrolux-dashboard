import { Select } from 'antd';
import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setCookingCourseFilterType } from '../../Redux/Slice/CookingCourseType/CookingCourseFilterType';
import { setEventByCancellationFilterType } from '../../Redux/Slice/EventByCancellation/EventByCancellationFilterType';
import { setEventByCategoryFilterType } from '../../Redux/Slice/EventByCategory/eventByCategoryFilterType';
import { setEventByLocationFilterType } from '../../Redux/Slice/EventByLocation/eventByLocationFilterType';
import { setEventByMonthFilterType } from '../../Redux/Slice/EventByMonth/eventByMonthFilterType';
import { setEventByStatusFilterType } from '../../Redux/Slice/EventByStatus/EventByStatusFilterType';
import { setEventByYearFilterType } from '../../Redux/Slice/EventByYear/eventByYearFilterType';
import { setEventPerSalesPersonFilterType } from '../../Redux/Slice/EventPerSalesPerson/EventPerSalesPersonFilterType';
import { setGlobalFilterTimelineFilterType } from '../../Redux/Slice/GlobalFilterTimeline/GlobalFilterTimelineFilterType';
import FilterTypeCustomDate from '../FilterTypeCustomDate/FilterTypeCustomDate';
import FilterTypeMonth from '../FilterTypeMonth/FilterTypeMonth';
import FilterTypeTimeFrame from '../FilterTypeTImeFrame/FilterTypeTimeFrame';
import FilterTypeYear from '../FilterTypeYear/FilterTypeYear';
import './Timeline.scss';

function Timeline() {
    const location = useSelector(state => state.location.value);

    const dispatch = useDispatch();
    const [filterType, setFilterType] = useState('years');
    const handleFilterType = value => {
        setFilterType(value);
    };

    useEffect(() => {
        switch (location) {
            case 'event-by-year-timeline':
                dispatch(setEventByYearFilterType(filterType));
                break;
            case 'event-by-location-timeline':
                dispatch(setEventByLocationFilterType(filterType));
                break;
            case 'event-by-category-timeline':
                dispatch(setEventByCategoryFilterType(filterType));
                break;

            case 'event-by-months-timeline':
                dispatch(setEventByMonthFilterType(filterType));
                break;
            case 'cooking-course-type-timeline':
                dispatch(setCookingCourseFilterType(filterType));
                break;
            case 'event-by-status-timeline':
                dispatch(setEventByStatusFilterType(filterType));
                break;

            case 'event-by-cancellation-timeline':
                dispatch(setEventByCancellationFilterType(filterType));
                break;
            case 'event-per-sales-person-timeline':
                dispatch(setEventPerSalesPersonFilterType(filterType));
                break;
            case 'global-timeline':
                dispatch(setGlobalFilterTimelineFilterType(filterType));
                break;

            default:
                console.log('filter type default', location);
        }
    }, [filterType]);

    useEffect(() => {
        setFilterType('years');
    }, []);

    return (
        <>
            <Select
                className="timeline-filter"
                defaultValue={filterType}
                style={{
                    width: '100%',
                }}
                onChange={handleFilterType}
                options={[
                    {
                        value: 'months',
                        label: 'Months',
                    },
                    {
                        value: 'years',
                        label: 'Years',
                    },
                    {
                        value: 'custom_date_range',
                        label: 'Custom Date Range',
                    },
                    {
                        value: 'custom_time_frame',
                        label: 'Custom Time Frame',
                    },
                ]}
            />
            {filterType === 'months' && <FilterTypeMonth />}
            {filterType === 'years' && <FilterTypeYear />}
            {filterType === 'custom_date_range' && <FilterTypeCustomDate />}
            {filterType === 'custom_time_frame' && <FilterTypeTimeFrame />}
        </>
    );
}

export default Timeline;
