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
import React, { useRef, useState } from 'react';
import { Bar } from 'react-chartjs-2';
import { useSelector } from 'react-redux';
import { useEventByCategoryQuery } from '../../API/apiSlice';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';
import './EventByCategory.scss';

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

    const [grapTableEvntCat, setGrapTableEvntCat] = useState('graph');
    const eventByCategoryFilterType = useSelector(state => state.eventByCategoryFilterType.value);
    const eventbyCategoryTimelineYears = useSelector(
        state => state.eventbyCategoryTimelineYears.value
    );

    const eventCategoryChartRef = useRef();

    const handleProductStatusChange = value => {
        console.log(value);
        setProductStatus(value);
    };

    // const payload = {
    //     type: requestData,
    //     timeline_type: eventByCategoryFilterType,
    //     timeline_filter: eventbyCategoryTimelineYears,
    //     filter_key_value: {
    //         product_status: productStatus,
    //     },
    // };
    const payload = {
        type: 'events',
        timeline_type: 'years',
        filter_key_value: {
            product_status: 'Planned',
        },
        timeline_filter: ['2022', '2023'],
    };

    const { error, data } = useEventByCategoryQuery(payload);
    console.log('cat', data);

    // Graph
    const labels = data && data.data.labels;
    const colors = ['#A6B2A4', '#6B7A66', '#3B4536', '#031C40', '#7B899B'];
    let graphData = {};
    if (data !== '') {
        try {
            graphData = {
                labels,
                datasets:
                    data &&
                    data.data.dataset.map((dataset, index) => ({
                        ...dataset,
                        backgroundColor: colors[index % colors.length],
                        barThickness: 24,
                        borderDash: [],
                        borderDashOffset: 0.0,
                    })),
            };
        } catch (e) {
            console.log(e);
        }
    }
    // Table
    const tableData = [];
    let columns = [];
    if (data !== '') {
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
                            value: 'Planned',
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
                : data && (
                        <Table
                            ref={eventCategoryChartRef}
                            columns={columns}
                            dataSource={tableData}
                            pagination={false}
                        />
                    )}
        </div>
    );
}

export default EventByCategory;
