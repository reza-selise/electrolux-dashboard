import { Select } from 'antd';
import React, { useState } from 'react';
import FilterTypeCustomDate from '../FilterTypeCustomDate/FilterTypeCustomDate';
import FilterTypeMonth from '../FilterTypeMonth/FilterTypeMonth';
import FilterTypeYear from '../FilterTypeYear/FilterTypeYear';
import './Timeline.scss';

function Timeline() {
    const [filterType, setFilterType] = useState('year');
    const handleFilterType = (value) => {
        setFilterType(value);
    };
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
                        value: 'month',
                        label: 'Month',
                    },
                    {
                        value: 'year',
                        label: 'Year',
                    },
                    {
                        value: 'custom-date-range',
                        label: 'Custom Date Range',
                    },
                    {
                        value: 'custom-time-frame',
                        label: 'Custom Time Frame',
                    },
                ]}
            />
            {filterType === 'month' && <FilterTypeMonth />}
            {filterType === 'year' && <FilterTypeYear />}
            {filterType === 'custom-date-range' && <FilterTypeCustomDate />}
            {filterType === 'custom-time-frame' && 'custom-time-frame sectected'}
        </>
    );
}

export default Timeline;
