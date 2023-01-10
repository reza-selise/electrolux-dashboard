import { Col, Row, Table } from 'antd';
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    // eslint-disable-next-line prettier/prettier
    Tooltip
} from 'chart.js';

import React, { useState } from 'react';
// eslint-disable-next-line import/no-unresolved
import { Bar } from 'react-chartjs-2';
import { useSelector } from 'react-redux';
import { useEventByMonthsQuery } from '../../API/apiSlice';
import { eluxTranslation } from '../../Translation/Translation';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';
import './EventByMonth.scss';

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
};

function EventByMonth() {
    const [requestData, setRequestData] = useState('events');
    const eventbyMonthTimelineYears = useSelector(state => state.eventbyMonthTimelineYears.value);
    const eventbyMonthTimelineMonth = useSelector(state => state.eventbyMonthTimelineMonth.value);
    const [graphTableforMonth, setgGrapOrTableForMonth] = useState('graph');
    const eventByMonthFilterType = useSelector(state => state.eventByMonthFilterType.value);

    const requestBody = eventbyMonthTimelineYears.map(year => ({
        year: year.toString(),
        months: eventbyMonthTimelineMonth.toString(),
    }));

    const eventTotals = {};

    const payload = {
        request_data: requestData,
        filter_type: eventByMonthFilterType,
        request_body: JSON.stringify(requestBody),
        event_status: 'planned',
        customer_type: 'all',
    };
    const { data, error, isLoading } = useEventByMonthsQuery(payload);

    // ************** dynamic years **********
    let years = [];
    years = data && data.data.years.map(item => item.year);

    let months = [];
    months = data && data.data.years.map(item => item.months);

    let formatedMonths = [];

    if (months) {
        formatedMonths = months[0].map(item => item.month);
    }

    //  data for table
    const columns = [
        {
            title: 'Month',
            dataIndex: 'month',
            key: 'month',
            width: 100,
        },
    ];
    // eslint-disable-next-line no-unused-expressions
    years &&
        years.forEach(year => {
            eventTotals[`b2b_${year}`] = 0;
            eventTotals[`b2c_${year}`] = 0;
            eventTotals[`elux_${year}`] = 0;
            columns.push({
                title: year,
                children: [
                    {
                        title: 'B2B',
                        dataIndex: `b2b_${year}`,
                        key: `b2b_${year}`,
                        width: 100,
                    },
                    {
                        title: 'B2C',
                        dataIndex: `b2c_${year}`,
                        key: `b2c_${year}`,
                        width: 100,
                    },
                    {
                        title: 'ELUX',
                        dataIndex: `elux_${year}`,
                        key: `elux_${year}`,
                        width: 100,
                    },
                ],
            });
        });

    const tableData = [];

    if (data && Object.keys(data).length > 0) {
        for (let i = 0; i < formatedMonths.length; i += 1) {
            const item = { key: i, month: formatedMonths[i] };
            // eslint-disable-next-line array-callback-return
            years.map((year, index) => {
                // console.log('Test: ', data && data.data.years[0].months[i].b2b);
                // console.log('I ', i);
                // const testValue1 = Math.ceil(Math.random() * 50);
                // const testValue2 = Math.ceil(Math.random() * 50);
                // const testValue3 = Math.ceil(Math.random() * 50);
                item[`b2b_${year}`] = data && data.data.years[index].months[i].b2b;
                item[`b2c_${year}`] = data && data.data.years[index].months[i].b2c;
                item[`elux_${year}`] = data && data.data.years[index].months[i].elux;
                eventTotals[`b2b_${year}`] = eventTotals[`b2b_${year}`] + item[`b2b_${year}`];
                eventTotals[`b2c_${year}`] = eventTotals[`b2c_${year}`] + item[`b2c_${year}`];
                eventTotals[`elux_${year}`] = eventTotals[`elux_${year}`] + item[`elux_${year}`];
                // item[`total_${year}`] = Math.ceil(Math.random()*50);
                // item[`elux_${year}`] = data && data.data.years[index].months[i].elux??Math.random()*50;
            });
            tableData.push({
                ...item,
            });
        }
    }

    // data for graph
    const chartjsDataSet = [];
    const columnColors = ['#041C3F', '#797285', '#79899B', '#D2BA96', '#697B68'];

    if (data && data.data) {
        const { years: resData } = data.data;

        if (resData) {
            resData.map((item, index) => {
                const singleDataSet = {};
                singleDataSet.label = item.year;

                if (item.months.length > 0) {
                    const data = [];
                    item.months.map(m => {
                        data.push(m.total);
                    });
                    singleDataSet.data = data;
                    singleDataSet.backgroundColor = columnColors[index];
                }
                chartjsDataSet.push(singleDataSet);
            });
        }
    }

    const graphData = {
        labels: formatedMonths,
        datasets: chartjsDataSet && chartjsDataSet,
    };
    const { pleaseWait } = eluxTranslation;

    return (
        <>
            <div className="header-wrapper">
                <GraphTableSwitch
                    grapOrTable={graphTableforMonth}
                    identifier={3}
                    setgGrapOrTable={setgGrapOrTableForMonth}
                    name="event-by-month"
                />
                <DownloadButton
                    identifier={3}
                    location="event-by-month-comment"
                    graphID="event-by-month-graph"
                />
            </div>

            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <h2 className="graph-title">
                    Overview of Events <span>by month</span>
                </h2>
                <div style={{ display: 'flex', columnGap: '10px' }} className="graph-overview">
                    <LocalFilter
                        showBoth="true"
                        requestData={requestData}
                        setRequestData={setRequestData}
                        location="event-by-months-timeline"
                    />
                </div>
            </div>

            {error ? (
                'error'
            ) : isLoading ? (
                pleaseWait
            ) : graphTableforMonth === 'graph' ? (
                <Bar
                    className="custom_month_graph"
                    options={options}
                    data={graphData}
                    id="event-by-month-graph"
                />
            ) : (
                <Table
                    className="custom_footer"
                    pagination={false}
                    columns={columns}
                    dataSource={tableData}
                    bordered
                    size="middle"
                    // eslint-disable-next-line react/no-unstable-nested-components
                    footer={() => (
                        <Row style={{ display: 'flex', justifyContent: 'space-around' }}>
                            <Col
                                span={1}
                                style={{
                                    padding: '12px 8px',
                                    textAlign: 'left',
                                }}
                            >
                                <strong>TOTALS:</strong>
                            </Col>
                            {Object.keys(eventTotals).map((event, index) => (
                                <Col
                                    style={{
                                        padding: '12px 8px',
                                        textAlign: 'center',
                                    }}
                                    key={index}
                                    span={1}
                                >
                                    {eventTotals[event]}
                                </Col>
                            ))}
                        </Row>
                    )}
                />
            )}
        </>
    );
}

export default EventByMonth;
