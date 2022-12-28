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
// eslint-disable-next-line import/no-unresolved
import { useSelector } from 'react-redux';
import { useEventByLocationQuery } from '../../API/apiSlice';
import './EventByLocation.scss';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

export const options = {
    plugins: {
        title: {
            display: false,
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
};
const { Column } = Table;

function EventByLocation() {
    const eventByLocationFilterType = useSelector(state => state.eventByLocationFilterType.value);
    const eventByLocationTimelineYears = useSelector(
        state => state.eventByLocationTimelineYears.value
    );
    const eventByLocationTimelineMonths = useSelector(
        state => state.eventByLocationTimelineMonths.value
    );

    const [requestDataForLocation, setRequestDataForLocation] = useState('events');

    const [grapOrTableForLocation, setgGrapOrTableForLocation] = useState('graph');
    const handleSwitchChange = e => {
        setgGrapOrTableForLocation(e.target.value);
    };
    // const requestBody = eventByLocationTimelineYears.map(year => ({
    //     year: year.toString(),
    //     months: eventByLocationTimelineMonths.toString(),
    // }));
    const requestBody = [
        {
            year: '2022',
            // months: '',
            months: '01,02,03,04,05,06,07,08,09,10,11,12',
        },
    ];
    const payload = {
        filter_type: 'years',
        request_data: 'events',
        locations: '188,191',
        request_body: JSON.stringify(requestBody),
    };
    // const { data, error, isLoading } = useEventByLocationQuery(payload);
    // const data = useEventByLocationQuery(payload);
    console.log('data-request-body', requestBody);
    // console.log('data-location', data);
    // console.log('data-location', data, 'error ', error, 'is-loading', isLoading);

    const wrapWithPromise = () =>
        new Promise((resolve, reject) => {
            try {
                const { data, error, isLoading } = useEventByLocationQuery(payload);
                resolve({ data, error, isLoading });
            } catch (e) {
                reject(e);
            }
        });

    wrapWithPromise()
        .then(result => {
            // code to handle successful execution
            console.log('data-location', result);
        })
        .catch(error => {
            // code to handle error
            console.log('data-location-error', error);
        });

    // const getLocations = () => ['191', '188', '183', '512', '507'];
    // const getDataFromLocation = (location, locationYear) =>
    //     eventByLocationTimelineYears.map(year => locationYear === year && location[year]);

    // const colors = ['#93735a', '#4a2016', '#a6b2a4', '#6b7a66', '#3b4536'];
    // const labels = getLocations();
    // const datasets = eventByLocationTimelineYears.map((year, index) => ({
    //     label: year,
    //     data: data && data.data.locations.map(location => location),
    //     backgroundColor: colors[index % colors.length],
    // }));

    // // console.log('datasets', datasets);

    // const graphData = {
    //     labels,
    //     datasets,
    // };

    // const { pleaseWait } = eluxTranslation;

    return (
        <>
            {/* <div className="header-wrapper">
                <GraphTableSwitch
                    grapOrTable={grapOrTableForLocation}
                    handleSwitchChange={handleSwitchChange}
                    name="event-by-location"
                />
                <DownloadButton />
            </div>
            <div className="graph-overview">
                <h2 className="graph-title">
                    Overview of Events per location <span>by year</span>
                </h2>
            </div>
            <LocalFilter
                requestData={requestDataForLocation}
                setRequestData={setRequestDataForLocation}
            />
            {error ? (
                'error'
            ) : isLoading ? (
                pleaseWait
            ) : grapOrTableForLocation === 'graph' ? (
                <Bar options={options} data={graphData} />
            ) : (
                <Table dataSource={data.data.years}>
                    <Column title="Year" dataIndex="year" key="year" />
                    <Column title="ELUX" dataIndex="elux" key="elux" />
                    <Column title="B2B" dataIndex="b2b" key="b2b" />
                    <Column title="B2C" dataIndex="b2c" key="b2c" />
                    <Column title="Total" dataIndex="total" key="total" />
                </Table>
            )} */}
        </>
    );
}

export default EventByLocation;
