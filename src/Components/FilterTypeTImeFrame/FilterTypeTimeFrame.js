import { Select } from 'antd';
import moment from 'moment';
import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setEventbyYearTimelineMonth } from '../../Redux/Slice/EventByYear/eventByYearTimelineMonth';
import { setEventbyYearTimelineYears } from '../../Redux/Slice/EventByYear/eventByYearTimelineYear';
import './FilterTypeTimeFrame.scss';

function FilterTypeTimeFrame() {
    const [time, setTime] = useState('1');
    const [frame, setFrame] = useState('years');
    const location = useSelector((state) => state.location.value);

    const dispatch = useDispatch();

    const handleTimeChange = (value) => {
        setTime(value);
    };

    const handleFrmaeChange = (value) => {
        setFrame(value);
    };

    const getYears = (value, lastYears) => {
        let now = moment().year();

        for (let i = 0; i < value; i += 1) {
            const lastYear = now - 1;

            lastYears.push(lastYear);

            now = lastYear;
        }
    };

    const getMonths = (value, years) => {
        const now = moment();
        const currentYear = now.year();

        const lastMonths = [];
        const numYears = Math.floor(value / 12);
        const monthsByYear = [];
        for (let i = 0; i < value; i += 1) {
            const lastMonth = now.subtract(1, 'month');

            lastMonths.push(lastMonth);
        }

        lastMonths.forEach((month) => {
            const year = month.year();
            if (!monthsByYear[year]) {
                monthsByYear[year] = [];
            }
            monthsByYear[year].push(month);
        });
        if (currentYear in monthsByYear) {
            monthsByYear[currentYear].push(now);
        }

        for (let year = currentYear; year >= currentYear - numYears; year -= 1) {
            if (year in monthsByYear) {
                years.push(year);
            } else {
                years.push(year);
                monthsByYear[year] = [];
            }
        }
    };

    useEffect(() => {
        const lastYears = [];
        const years = [];

        switch (location) {
            case 'event-by-year-timeline':
                if (frame === 'years') {
                    getYears(time, lastYears);
                    dispatch(setEventbyYearTimelineYears(lastYears));
                    dispatch(setEventbyYearTimelineMonth([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]));
                } else {
                    getMonths(time, years);
                    dispatch(setEventbyYearTimelineYears(years));
                    dispatch(setEventbyYearTimelineMonth([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]));
                }
                break;

            default:
                console.log('filter type year month default');
        }
    }, [time, frame]);
    return (
        <div className="filter-type-time-frame">
            <span>Last</span>
            <Select
                className="time-frame-filter"
                defaultValue={time}
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
                    {
                        value: '50',
                        label: '50',
                    },
                ]}
            />
            <Select
                className="time-frame-filter"
                defaultValue={frame}
                style={{
                    width: '100%',
                }}
                onChange={handleFrmaeChange}
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
