// eslint-disable-next-line import/no-unresolved
import { Table } from 'antd';
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    Tooltip
} from 'chart.js';
import React, { useState } from 'react';
import { useSelector } from 'react-redux';
// eslint-disable-next-line import/no-unresolved
import { Bar } from 'react-chartjs-2';
import { eluxTranslation } from '../../Translation/Translation';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';

import { useEventByLocationQuery } from '../../API/apiSlice';
import './EventByLocation.scss';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const BarOptions = {
    plugins: {
        title: {
            display: false,
        },
        legend: {
            align: 'start',
            labels: {
                boxWidth: 16,
                boxHeight: 16,
            },
        },
    },
    responsive: true,
    scales: {
        x: {
            stacked: true,
        },
        y: {
            stacked: true,
        },
    },
    barPercentage: 1,
};
const { Column } = Table;
function EventByLocation() {
    const eventByLocationFilterType = useSelector(state => state.eventByLocationFilterType.value);
    const eventByLocationTimelineYears = useSelector(
        state => state.eventByLocationTimelineYears.value
    );
    const eventbyLocationTimelineMonth = useSelector(
        state => state.eventbyLocationTimelineMonth.value
    );

    const [grapOrTableForLocation, setgGrapOrTableForLocation] = useState('graph');
    const [requestDataForLocation, setRequestDataForLocation] = useState('events');
    const locationType = useSelector(state => state.locationType.value);
    const customerType = useSelector(state => state.customerType.value);

    const requestBody = eventByLocationTimelineYears.map(year => ({
        year: year.toString(),
        months: eventbyLocationTimelineMonth.toString(),
    }));
    const getLocations = () => ['191', '188', '501', '502', '504'];

    const payload = {
        filter_type: eventByLocationFilterType,
        request_data: requestDataForLocation,
        event_status: 'reserved,planned,took_place', // all
        customer_type:customerType,
        booking_type: 'Manual Booking, Walk-in',
        sales_employee: '1,2,3',
        categories: '93,30,18,21',
        locations:locationType,
        request_body: JSON.stringify(requestBody),
    };
    const { data, error, isLoading } = useEventByLocationQuery(payload);
    // console.log('data-location :', data, error, isLoading);
    const getDataFromLocation = (location, locationYear) =>
        eventByLocationTimelineYears.map(year => (locationYear === year ? location[year] : 0));

    const colors = ['#93735a', '#4a2016', '#a6b2a4', '#6b7a66', '#3b4536'];

    const labels = data && data.data.locations.map(location => location.location);
    const datasets = eventByLocationTimelineYears.map((year, index) => ({
        label: year,
        data: data && data.data.locations.map(location => getDataFromLocation(location, year)),
        backgroundColor: colors[index % colors.length],
    }));
    const graphData = {
        labels,
        datasets,
    };
    // console.log('data-location-dataset:', eventByLocationTimelineYears);

    const { pleaseWait } = eluxTranslation;
    return (
        <>
            <div className="header-wrapper">
                <GraphTableSwitch
                    identifier={1}
                    grapOrTable={grapOrTableForLocation}
                    setgGrapOrTable={setgGrapOrTableForLocation}
                    name="event-by-location"
                />
                <DownloadButton
                    location="event-by-location-comment"
                    graphID="event-by-location-graph"
                />
            </div>
            <div className="graph-overview location-graph">
                <h2 className="graph-title">
                    Overview of Events per location <span>by year</span>
                </h2>
                <LocalFilter
                    showBoth="true"
                    requestData={requestDataForLocation}
                    setRequestData={setRequestDataForLocation}
                    location="event-by-location-timeline"
                />
            </div>

            {error ? (
                'error'
            ) : isLoading ? (
                pleaseWait
            ) : grapOrTableForLocation === 'graph' ? (
                <Bar options={BarOptions} data={graphData} id="event-by-location-graph" />
            ) : (
                <Table
                    dataSource={data.data.locations}
                    pagination={false}
                    className="event-by-location-table"
                >
                    <Column title="Location" dataIndex="location" key="location" />
                    {eventByLocationTimelineYears.map(year => (
                        <Column title={year} dataIndex={year} key={year} />
                    ))}
                    <Column title="Total" dataIndex="total" key="total" />
                </Table>
            )}
        </>
    );
}

export default EventByLocation;
