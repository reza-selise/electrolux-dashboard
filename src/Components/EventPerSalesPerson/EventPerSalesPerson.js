import React, { useState } from 'react';
import { useEventPerSalesPersonQuery } from '../../API/apiSlice';
import DownloadButton from '../DownloadButton/DownloadButton';
import LocalFilter from '../LocalFilter/LocalFilter';
import './EventPerSalesPerson.scss';

function TableView({ data }) {
    const columns = data.table_header.map(label => ({
        title: label,
        key: label,
    }));
    const dataSource = data.table_rows;

    return (
        <table className="event-per-sales-person-table">
            <thead>
                <tr>
                    {columns.map(item => (
                        <th key={item.key}>{item.title} </th>
                    ))}
                </tr>
            </thead>
            <tbody>
                {dataSource.map((item, index) => (
                    <tr key={index}>
                        {item.map((data, index) => (
                            <td key={index}>{data} </td>
                        ))}
                    </tr>
                ))}
            </tbody>
        </table>
    );
}
function EventPerSalesPerson() {
    const [payload, setPayload] = useState({
        type: 'events',
        timeline_type: 'years',
        timeline_filter: ['2022', '2021', '2020', '2019', '2018'],
        filter_key_value: {},
    });
    const { error, isLoading, data } = useEventPerSalesPersonQuery(payload);

    return (
        <div className="event-per-sales-person">
            <h2 className="graph-title">Overview of Sales Events per Salesperson</h2>
            <div className="graph-overview">
                <DownloadButton identifier={8} />
                <LocalFilter showBoth="false" location="event-per-sales-person-timeline" />
            </div>
            {error ? 'Error' : isLoading ? 'Please wait' : data && <TableView data={data.data} />}
        </div>
    );
}

export default EventPerSalesPerson;
