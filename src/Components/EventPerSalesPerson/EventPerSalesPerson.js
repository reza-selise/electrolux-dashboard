import React, { useState } from 'react';
import { useEventPerSalesPersonQuery } from '../../API/apiSlice';

const TableView = data => 'This is table';

function EventPerSalesPerson() {
    const [payload, setPayload] = useState({
        type: 'events',
        timeline_type: 'years',
        timeline_filter: ['2022', '2021', '2020', '2019', '2018'],
        filter_key_value: {},
    });
    const { error, isLoading, data } = useEventPerSalesPersonQuery(payload);
    console.log('dddd', data);
    return (
        <div>{error ? 'Error' : isLoading ? 'Please wait' : data && <TableView data={data} />}</div>
    );
}

export default EventPerSalesPerson;
