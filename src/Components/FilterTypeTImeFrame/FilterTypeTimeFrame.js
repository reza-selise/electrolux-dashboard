import { Select } from 'antd';
import React from 'react';
import './FilterTypeTimeFrame.scss';

function FilterTypeTimeFrame() {
    const handleTimeChange = (value) => {
        console.log(value);
    };
    return (
        <div className="filter-type-time-frame">
            <span>Last</span>
            <Select
                className="time-frame-filter"
                defaultValue="1"
                style={{
                    width: '100%',
                }}
                onChange={handleTimeChange}
                options={[
                    {
                        value: '1',
                        label: '1',
                    },
                    {
                        value: '2',
                        label: '2',
                    },
                ]}
            />
            <Select
                className="time-frame-filter"
                defaultValue="years"
                style={{
                    width: '100%',
                }}
                onChange={handleTimeChange}
                options={[
                    {
                        value: 'months',
                        label: 'Months',
                    },
                    {
                        value: 'years',
                        label: 'Years',
                    },
                ]}
            />
        </div>
    );
}

export default FilterTypeTimeFrame;
