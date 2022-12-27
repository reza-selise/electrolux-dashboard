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
import { useEventByCancellationQuery } from '../../API/apiSlice';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';
import './EventByCancellation.scss';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);
export const options = {
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
};

function MyTable({ data }) {
    const dataSource = [
        {
            key: '1',
            year: "Canceled by customer",
            year2019: 15,
            year2018: 3,
            year2017: 35,
            year2016: 35,
            total: 123,
        },
       
    ];

    const columns = [
        {
            title: 'Year',
            dataIndex: 'year',
            key: 'year',
        },

        {
            title: '2019',
            dataIndex: 'year2019',
            key: 'year2019',
        },
        {
            title: '2018',
            dataIndex: 'year2018',
            key: 'year2018',
        },
        {
            title: '2017',
            dataIndex: 'year2017',
            key: 'year2017',
        },
        {
            title: 'Total',
            dataIndex: 'total',
            key: 'total',
        },
    ];

    return <Table dataSource={dataSource} columns={columns} />;
}

function EventByCancellation() {
    const [requestData, setRequestData] = useState('events');
    const [grapTableCookingCourse, setGrapTableCookingCourse] = useState('graph');
    const [productStatus, setProductStatus] = useState('Took Place');
    // const [graphData, setGraphData] = useState();

    const payload = {
        type: requestData,
        timeline_type: 'years',
        timeline_filter: ['2022', '2024', '2021', '2020'],
        filter_key_value: {},
    };
    const { data, isLoading } = useEventByCancellationQuery(payload);
    console.log(data, 'data of event cancel');

    const handleProductStatusChange = value => {
        setProductStatus(value);
    };
    const colors = ['#937359', '#4A2017', '#A6B2A4', '#6B7A66', '#3B4536'];

    const graphData =
        isLoading === false
            ? {
                  labels: data.data.labels ? data.data.labels : [],
                  datasets: data.data.datasets.map((dataset, index) => ({
                      ...dataset,
                      backgroundColor: colors[index % colors.length],
                      barThickness: 24,
                      borderDash: [],
                      borderDashOffset: 0.0,
                  })),
              }
            : [];

    return (
        <div>
            <div className="header-wrapper">
                <GraphTableSwitch
                    grapOrTable={grapTableCookingCourse}
                    setgGrapOrTable={setGrapTableCookingCourse}
                    name="event-by-cancellation"
                />
                <DownloadButton identifier={8} />
            </div>
            <h2 className="graph-title">Overview of Event Cancellation</h2>
            <div className="graph-overview cancellation_graph">
                <LocalFilter
                    requestData={requestData}
                    setRequestData={setRequestData}
                    location="event-by-cancellation-timeline"
                />
            </div>

            {grapTableCookingCourse === 'graph' ? (
                isLoading === false ? (
                    <Bar options={options} data={graphData} />
                ) : (
                    'Loading'
                )
            ) : (
                data && <MyTable data={data.data} />
            )}
        </div>
    );
}

export default EventByCancellation;
