import { Select } from 'antd';
import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setEventByYearFilterType } from '../../Redux/Slice/EventByYear/eventByYearFilterType';
import FilterTypeCustomDate from '../FilterTypeCustomDate/FilterTypeCustomDate';
import FilterTypeMonth from '../FilterTypeMonth/FilterTypeMonth';
import FilterTypeTimeFrame from '../FilterTypeTImeFrame/FilterTypeTimeFrame';
import FilterTypeYear from '../FilterTypeYear/FilterTypeYear';
import './Timeline.scss';

function Timeline() {
    const eventByYearFilterType = useSelector((state) => state.eventByYearFilterType.value);
    const location = useSelector((state) => state.location.value);

    const dispatch = useDispatch();
    // const [filterType, setFilterType] = useState('year');
    const handleFilterType = (value) => {
        switch (location) {
            case 'event-by-year-timeline':
                dispatch(setEventByYearFilterType(value));
                break;

            case 'event-by-months-timeline':
                dispatch(setEventByYearFilterType(value));
                break;

            default:
                console.log('filter type year month default');
        }
    };
    return (
        <>
            <Select
                className="timeline-filter"
                defaultValue={eventByYearFilterType}
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
            {eventByYearFilterType === 'months' && <FilterTypeMonth />}
            {eventByYearFilterType === 'years' && <FilterTypeYear />}
            {eventByYearFilterType === 'custom_date_range' && <FilterTypeCustomDate />}
            {eventByYearFilterType === 'custom_time_frame' && <FilterTypeTimeFrame />}
        </>
    );
}

export default Timeline;
