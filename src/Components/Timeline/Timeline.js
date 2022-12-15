import { Select } from 'antd';
import React, { useState } from 'react';
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
            {filterType === 'month' && 'month sectected'}
            {filterType === 'year' && <FilterTypeYear />}
            {filterType === 'custom-date-range' && 'custom-date-range sectected'}
            {filterType === 'custom-time-frame' && 'custom-time-frame sectected'}
        </>
    );
}

export default Timeline;
