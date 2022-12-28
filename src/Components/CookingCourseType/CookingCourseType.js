import { Select, Table } from 'antd';
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
import { useCookingCourseTypeQuery } from '../../API/apiSlice';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';

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
            name: 'Mike',
            age: 32,
            address: '10 Downing Street',
        },
        {
            key: '2',
            name: 'John',
            age: 42,
            address: '10 Downing Street',
        },
    ];

    const columns = [
        {
            title: 'Name',
            dataIndex: 'name',
            key: 'name',
        },
        {
            title: 'Age',
            dataIndex: 'age',
            key: 'age',
        },
        {
            title: 'Address',
            dataIndex: 'address',
            key: 'address',
        },
    ];

    return <Table dataSource={dataSource} columns={columns} />;
}

function CookingCourseType() {
    const [requestData, setRequestData] = useState('events');
    const [grapTableCookingCourse, setGrapTableCookingCourse] = useState('graph');
    const [productStatus, setProductStatus] = useState('Took Place');
    // const [graphData, setGraphData] = useState();
    const payload = {
        type: 'events',
        timeline_type: 'years',
        timeline_filter: ['2022', '2024', '2020'],
        filter_key_value: {},
    };
    const { data, isLoading } = useCookingCourseTypeQuery(payload);

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
                    name="cooking-course-type"
                />
                <DownloadButton identifier={7} />
            </div>
            <h2 className="graph-title">Overview of Cooking Course Type</h2>
            <div className="graph-overview">
                <LocalFilter
                    requestData={requestData}
                    setRequestData={setRequestData}
                    location="event-by-category-timeline"
                />
                <Select
                    defaultValue={productStatus}
                    style={{ width: 120 }}
                    onChange={handleProductStatusChange}
                    options={[
                        {
                            value: 'Took Place',
                            label: 'Taken Place',
                        },
                        {
                            value: 'Planned',
                            label: 'Planned',
                        },
                        {
                            value: 'Reserved',
                            label: 'Reserved',
                        },
                    ]}
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

export default CookingCourseType;
