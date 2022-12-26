import { Select } from 'antd';
import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setEventByCategoryFilterType } from '../../Redux/Slice/EventByCategory/eventByCategoryFilterType';
import { setEventByYearFilterType } from '../../Redux/Slice/EventByYear/eventByYearFilterType';
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
            case 'event-by-category-timeline':
                dispatch(setEventByCategoryFilterType(filterType));
                break;

            default:
                console.log('filter type default');
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
