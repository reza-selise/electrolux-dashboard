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
import { useSelector } from 'react-redux';
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
    const dataSource = [];

    data.labels.forEach((label, outerIndex) => {
        const row = {
            key: outerIndex,
            year: label,
        };
        let total = 0
        data.datasets.forEach((data, index) => {
            row[`year${data.label}`] = data.data[outerIndex];
            total = total + parseInt(data.data[outerIndex])
        });
        dataSource.push({...row,total:total});

       
    });
    const columns = [
        {
            title: 'Reason For Cancellation',
            dataIndex: 'year',
            key: 'year',
        },
    ];

    data.datasets.forEach((item, index) => {
        const column = {
            title: item.label,
            dataIndex: `year${item.label}`,
            key: index,
        };
        columns.push(column);
    });

    columns.push({
        title: 'Total',
        dataIndex: 'total',
        key: 'total',
    });

    return <Table dataSource={dataSource} columns={columns} />;
}

function EventByCancellation() {
    const [requestData, setRequestData] = useState('events');
    const [grapTableEventCancellation, setGrapTableEventCancellation] = useState('graph');
    const [productStatus, setProductStatus] = useState('Took Place');
    // const [payload, setPayload] = useState();
    const eventByCancellationFilterType = useSelector(state => state.eventByCancellationFilterType.value);
    const eventByCancellationYears = useSelector(state => state.eventByCancellationYears.value);
    const eventByCancellationMonths = useSelector(state => state.eventByCancellationMonths.value);
    const eventByCancellationYearMonths = useSelector(state => state.eventByCancellationYearMonths.value);
    const eventByCancellationCustomDate = useSelector(state => state.eventByCancellationCustomDate.value);
    // const [graphData, setGraphData] = useState();


    // useEffect(() => {
    //     console.log('eventByCancellationFilterType', eventByCancellationFilterType);
    //     switch (eventByCancellationFilterType) {
    //         case 'years':
    //             setPayload({
    //                 type: requestData,
    //                 timeline_type: 'years',
    //                 timeline_filter: eventByCancellationYears,
    //                 filter_key_value: {
    //                     product_status: productStatus,
    //                 },
    //                 year_months: eventByCancellationYearMonths,
    //             });
    //             break;

    //         case 'months':
    //             setPayload({
    //                 type: requestData,
    //                 timeline_type: 'months',
    //                 timeline_filter: eventByCancellationMonths,
    //                 filter_key_value: {
    //                     product_status: productStatus,
    //                 },
    //             });
    //             break;
    //         case 'custom_date_range':
    //             setPayload({
    //                 type: requestData,
    //                 timeline_type: 'custom_date_range',
    //                 timeline_filter: eventByCancellationCustomDate,
    //                 filter_key_value: {
    //                     product_status: productStatus,
    //                 },
    //             });
    //             break;
    //         case 'custom_time_frame':
    //             setPayload({
    //                 type: requestData,
    //                 timeline_type: 'custom_time_frame',
    //                 timeline_filter: eventByCancellationMonths,
    //                 filter_key_value: {
    //                     product_status: productStatus,
    //                 },
    //             });
    //             break;

    //         default:
    //             setPayload({
    //                 type: 'events',
    //                 timeline_type: 'years',
    //                 timeline_filter: ['2022', '2021', '2020', '2019', '2018'],
    //                 filter_key_value: {},
    //             });
    //             console.log('Cooking course type default payload');
    //     }
    // }, [
    //     requestData,
    //     eventByCancellationFilterType,
    //     eventByCancellationYears,
    //     eventByCancellationMonths,
    //     productStatus,
    //     eventByCancellationYearMonths,
    //     eventByCancellationCustomDate,
    // ]);
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
                    grapOrTable={grapTableEventCancellation}
                    setgGrapOrTable={setGrapTableEventCancellation}
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

            {grapTableEventCancellation === 'graph' ? (
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
