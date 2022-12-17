import { Select } from 'antd';
import React, { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { setEventbyYearTimelineYears } from '../../Redux/Slice/eventByYearTimelineYear';

function FilterTypeYear() {
    const [years, setYears] = useState([]);

    const currentYear = new Date().getFullYear();

    const dispatch = useDispatch();

    const handleYearChange = (value) => {
        console.log(value);
        dispatch(setEventbyYearTimelineYears(value));
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
    return (
        <Select
            defaultValue={currentYear}
            style={{
                width: '100%',
            }}
            mode="multiple"
            onChange={handleYearChange}
            options={years}
        />
    );
}

export default FilterTypeYear;
