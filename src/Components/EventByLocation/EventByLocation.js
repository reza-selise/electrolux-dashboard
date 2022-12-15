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
import { Bar } from 'react-chartjs-2';
import { useEventByYearQuery } from '../../API/apiSlice';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';
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
    const [requestData, setRequestData] = useState('events');
    console.log('re', requestData);

    const [grapOrTableForLocation, setgGrapOrTableForLocation] = useState('graph');
    const handleSwitchChange = (e) => {
        setgGrapOrTableForLocation(e.target.value);
        console.log(e.target.value);
    };
    const payload = {
        request_data: 'events',
        filter_type: 'years',
        request_body: JSON.stringify([
            {
                year: '2022',
                months: '09,10,11',
            },
        ]),
    };
    const { data } = useEventByYearQuery(payload);
    const labels =
        data && data.data.years.map((year) => year.year)
            ? data.data.years.map((year) => year.year)
            : ['2022'];
    const graphData = {
        labels,
        datasets: [
            {
                label: 'ELUX',
                data:
                    data && data.data.years.map((year) => year.elux)
                        ? data.data.years.map((year) => year.elux)
                        : 0,
                backgroundColor: '#4A2017',
            },
            {
                label: 'B2B',
                data:
                    data && data.data.years.map((year) => year.b2b)
                        ? data.data.years.map((year) => year.b2b)
                        : 0,
                backgroundColor: '#937359',
            },
            {
                label: 'B2C',
                data:
                    data && data.data.years.map((year) => year.b2c)
                        ? data.data.years.map((year) => year.b2c)
                        : 0,
                backgroundColor: '#D0B993',
            },
        ],
    };
    return (
        <>
            <div className="header-wrapper">
                <GraphTableSwitch
                    grapOrTable={grapOrTableForLocation}
                    handleSwitchChange={handleSwitchChange}
                />
                <DownloadButton />
            </div>
            <div className="graph-overview">
                <h2 className="graph-title">
                    Overview of Events per location <span>by year</span>
                </h2>
            </div>
            <LocalFilter requestData={requestData} setRequestData={setRequestData} displayClass={"d-block"}/>
            {grapOrTableForLocation === 'graph' ? (
                <Bar options={options} data={graphData} />
            ) : (
                <Table dataSource={data.data.years}>
                    <Column title="Year" dataIndex="year" key="year" />
                    <Column title="ELUX" dataIndex="elux" key="elux" />
                    <Column title="B2B" dataIndex="b2b" key="b2b" />
                    <Column title="B2C" dataIndex="b2c" key="b2c" />
                    <Column title="Total" dataIndex="total" key="total" />
                </Table>
            )}
        </>
    );
}

export default EventByLocation;
