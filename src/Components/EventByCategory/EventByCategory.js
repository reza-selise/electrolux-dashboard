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
import { Bar } from 'react-chartjs-2';
import { useEventByCategoryQuery } from '../../API/apiSlice';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';
import './EventByCategory.scss';

const { Column } = Table;

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
function EventByCategory() {
    const [requestData, setRequestData] = useState('events');
    const [productStatus, setProductStatus] = useState('taken-place');
    console.log('category data', requestData);

    const [grapTableEvntCat, setGrapTableEvntCat] = useState('graph');

    const handleProductStatusChange = value => {
        console.log(value);
        setProductStatus(value);
    };

    const payload = {
        type: requestData,
        timeline_type: 'year',
        timeline_filter: ['2022', '2024', '2021'],
        filter_key_value: {},
    };
    const { error, data } = useEventByCategoryQuery(payload);

    // Graph
    const labels = data && data.data.labels;
    const colors = ['#A6B2A4', '#6B7A66', '#3B4536', '#031C40', '#7B899B'];
    const graphData = {
        labels,
        datasets:
            data &&
            data.data.dataset.map((dataset, index) => ({
                ...dataset,
                backgroundColor: colors[index % colors.length],
            })),
    };
    // Table
    const tableData = [];
    let columns = [];
    if (data !== null) {
        console.log('data', data);
        try {
            columns = [
                {
                    title: 'Year',
                    dataIndex: 'year',
                },
                ...data.data.labels.map(label => ({
                    title: label,
                    dataIndex: label,
                })),
            ];

            // Create table data
            data.data.dataset.forEach(yearData => {
                const year = yearData.label;

                tableData.push({
                    key: year,
                    year,
                    ...yearData.data.reduce((acc, val, i) => ({ ...acc, [labels[i]]: val }), {}),
                });
            });
        } catch (e) {
            console.log(e);
        }
    }

    // Render
    return (
        <div className="event-by-category">
            <div className="header-wrapper">
                <GraphTableSwitch
                    grapOrTable={grapTableEvntCat}
                    setgGrapOrTable={setGrapTableEvntCat}
                    name="event-by-category"
                />
                <DownloadButton identifier={4} />
            </div>
            <h2 className="graph-title">Overview of Events Category</h2>
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
                            value: 'taken-place',
                            label: 'Taken Place',
                        },
                        {
                            value: 'planned',
                            label: 'Planned',
                        },
                        {
                            value: 'cancelled',
                            label: 'Cancelled',
                        },
                    ]}
                />
            </div>

            {error
                ? 'error'
                : grapTableEvntCat === 'graph'
                    ? data && <Bar id="eventCategoryChartRef" options={options} data={graphData} />
                    : data && <Table columns={columns} dataSource={tableData} pagination={false} />}
        </div>
    );
}

export default EventByCategory;