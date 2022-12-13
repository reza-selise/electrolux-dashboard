import React from 'react';
import { useEventByYearQuery } from '../../API/apiSlice';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';

function EventByYear() {
    const payload = {
        request_data: 'events',
        filter_type: 'years',
        request_body: JSON.stringify([
            {
                year: '2022',
                months: '09,10,11',
            },
            // },
            // {
            //     year: '2023',
            //     months: '02,11,06',
            // },
            // {
            //     year: '2021',
            //     months: '02,07,04',
            // },
        ]),
    };
    const { data, error, isLoading } = useEventByYearQuery(payload);
    console.log('isload', isLoading);
    console.log('is Error', error);
    console.log('data', data);
    return (
        <>
            <GraphTableSwitch />
            <GraphTableSwitch />
        </>
    );
}

export default EventByYear;
