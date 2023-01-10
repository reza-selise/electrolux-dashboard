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
import React, { useEffect, useState } from 'react';
// eslint-disable-next-line import/no-unresolved
import { Bar } from 'react-chartjs-2';
import { useSelector } from 'react-redux';
import { useEventByCancellationQuery } from '../../API/apiSlice';
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
    const dataSource = [];

    data.labels.forEach((label, outerIndex) => {
        const row = {
            key: outerIndex,
            year: label,
        };
        let total = 0;
        data.datasets.forEach((data, index) => {
            row[`year${data.label}`] = data.data[outerIndex];
            total += parseInt(data.data[outerIndex]);
        });
        dataSource.push({ ...row, total });
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
    const [payload, setPayload] = useState();
    const eventByCancellationFilterType = useSelector(
        state => state.eventByCancellationFilterType.value
    );
    const eventByCancellationYears = useSelector(state => state.eventByCancellationYears.value);
    const eventByCancellationMonths = useSelector(state => state.eventByCancellationMonths.value);
    const eventByCancellationYearMonths = useSelector(
        state => state.eventByCancellationYearMonths.value
    );
    const eventByCancellationCustomDate = useSelector(
        state => state.eventByCancellationCustomDate.value
    );

    const customerType = useSelector(state => state.customerType.value);
    // const locationType = useSelector(state => state.locationType.value);
    const eventStatusType = useSelector(state => state.eventStatusType.value);
    const typeOfData = useSelector(state => state.typeOfData.value);
    const mainCategoryType = useSelector(state => state.mainCategoryType.value);
    const fbLeadType = useSelector(state => state.fbLeadType.value);

    // const [graphData, setGraphData] = useState();

    useEffect(() => {
        console.log('eventByCancellationFilterType', eventByCancellationFilterType);
        switch (eventByCancellationFilterType) {
            case 'years':
                setPayload({
                    type: requestData,
                    timeline_type: 'years',
                    timeline_filter: eventByCancellationYears,
                    filter_key_values: {
                        event_status: eventStatusType,
                        customer_types: customerType,
                        data_types: typeOfData,
                        categories: mainCategoryType,
                        fb_leads: fbLeadType,
                    },
                    year_months: eventByCancellationYearMonths,
                });
                break;

            case 'months':
                setPayload({
                    type: requestData,
                    timeline_type: 'months',
                    timeline_filter: eventByCancellationMonths,
                    filter_key_values: {
                        event_status: eventStatusType,
                        customer_types: customerType,
                        data_types: typeOfData,
                        categories: mainCategoryType,
                        fb_leads: fbLeadType,
                    },
                });
                break;
            case 'custom_date_range':
                setPayload({
                    type: requestData,
                    timeline_type: 'custom_date_range',
                    timeline_filter: eventByCancellationCustomDate,
                    filter_key_values: {
                        event_status: eventStatusType,
                        customer_types: customerType,
                        data_types: typeOfData,
                        categories: mainCategoryType,
                        fb_leads: fbLeadType,
                    },
                });
                break;
            case 'custom_time_frame':
                setPayload({
                    type: requestData,
                    timeline_type: 'custom_time_frame',
                    timeline_filter: eventByCancellationMonths,
                    filter_key_values: {
                        event_status: eventStatusType,
                        customer_types: customerType,
                        data_types: typeOfData,
                        categories: mainCategoryType,
                        fb_leads: fbLeadType,
                    },
                });
                break;

            default:
                setPayload({
                    type: 'events',
                    timeline_type: 'years',
                    timeline_filter: ['2022', '2024', '2021', '2020'],
                    filter_key_values: {},
                });
                console.log('Event By Cancellation default payload');
        }
    }, [
        requestData,
        eventByCancellationFilterType,
        eventByCancellationYears,
        eventByCancellationMonths,
        eventStatusType,
        customerType,
        typeOfData,
        fbLeadType,
        mainCategoryType,
        eventByCancellationYearMonths,
        eventByCancellationCustomDate,
    ]);
    // const payload = {
    //     type: requestData,
    //     timeline_type: 'years',
    //     timeline_filter: ['2022', '2024', '2021', '2020'],
    //     filter_key_value: {},
    // };
    const { data, isLoading } = useEventByCancellationQuery(payload);
    console.log(data, 'data of event cancel by shuvo');
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
                <DownloadButton
                    identifier={8}
                    location="event-by-cancellation-comment"
                    graphID="event-by-cancellation-graph"
                />
            </div>

            <div className="graph-overview">
                <h2 className="graph-title">Overview of Event Cancellation</h2>
                <LocalFilter
                    showBoth="true"
                    requestData={requestData}
                    setRequestData={setRequestData}
                    location="event-by-cancellation-timeline"
                />
            </div>

            {grapTableEventCancellation === 'graph' ? (
                isLoading === false ? (
                    <Bar options={options} data={graphData} id="event-by-cancellation-graph" />
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
