import { Col, Row, Table } from 'antd';
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
import { useEventByMonthsQuery } from '../../API/apiSlice';
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
    },
    responsive: true,
};
const { Column } = Table;

function EventByMonth() {
    const [requestData, setRequestData] = useState('events');

    const [graphTableforMonth, setgGrapOrTableForMonth] = useState('graph');

    const handleSwitchChange = (e) => {
        setgGrapOrTableForMonth(e.target.value);
    };
    const eventTotals = {};

    const payload = {
        request_data: requestData,
        filter_type: 'months',
        request_body: JSON.stringify([
            {
                year: '2022',
                months: '01,02,03,04,05,06,07,08,09,10,11,12',
            },
            {
                year: '2021',
                months: '01,02,03,04,05,06,07,08,09,10,11,12',
            },
            {
                year: '2020',
                months: '01,02,03,04,05,06,07,08,09,10,11,12',
            },
        ]),
    };
    const { data, isLoading } = useEventByMonthsQuery(payload);

    // const yearColumnData =
    //     data &&
    //     data.data.years.map((item) => ({ title: item.year, children: ['B2B', 'B2C', 'ELUX'] }));

    // ************** dynamic years **********
    let years = [];
    years = data && data.data.years.map((item) => item.year);

    // const monthColumnData = data && data.data.years.map((item) => item.months);

    // // data for table
    const columns = [
        {
            title: 'Month',
            dataIndex: 'month',
            key: 'month',
            width: 100,
        },
    ];
    years &&
        years.forEach((year) => {
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
                    // {
                    //     title: 'TOTAL',
                    //     dataIndex: `total_${year}`,
                    //     key: `total_${year}`,
                    // },
                ],
            });
        });

    // yearColumnData &&
    //     yearColumnData.map((data) => {
    //         columns.push({
    //             title: data.title,
    //             children:
    //                 data.children &&
    //                 data.children.map((item) => ({
    //                     title: item,
    //                     dataIndex: item.toLowerCase(),
    //                     key: item.toLowerCase(),
    //                 })),
    //         });
    //     });

    const tableData = [];
    console.log('Data: ', data && data);
    const staticMonths = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec',
    ];

    if (data && Object.keys(data).length > 0) {
        for (let i = 0; i < 12; i++) {
            const item = { key: i, month: staticMonths[i] };
            years.map((year, index) => {
                // console.log('Test: ', data && data.data.years[0].months[i].b2b);
                // console.log('I ', i);
                const testValue1 = Math.ceil(Math.random() * 50);
                const testValue2 = Math.ceil(Math.random() * 50);
                const testValue3 = Math.ceil(Math.random() * 50);
                item[`b2b_${year}`] = testValue1;
                item[`b2c_${year}`] = testValue2;
                item[`elux_${year}`] = testValue3;
                eventTotals[`b2b_${year}`] = eventTotals[`b2b_${year}`] + testValue1;
                eventTotals[`b2c_${year}`] = eventTotals[`b2c_${year}`] + testValue2;
                eventTotals[`elux_${year}`] = eventTotals[`elux_${year}`] + testValue3;
                // item[`total_${year}`] = Math.ceil(Math.random()*50);
                // item[`elux_${year}`] = data && data.data.years[index].months[i].elux??Math.random()*50;
            });
            tableData.push({
                ...item,
            });
        }
    }

    console.log('Table Data: ', tableData);
    console.log('Event Total: ', eventTotals);
    // console.log('Test Data: ', data);
    // const getTableColumns = (data) => {
    //     if (data) {
    //         const { years: columnsData } = data.data;
    //         const columnsSet = [
    //             {
    //                 title: 'Month',
    //                 dataIndex: 'month',
    //                 key: 'month',
    //                 render: (text) => <p>{text}</p>,
    //             },
    //         ];

    //         if (columnsData.length > 0) {
    //             columnsData.map((item) => {
    //                 const columnItem = {
    //                     title: item.year,
    //                     children: [
    //                         {
    //                             title: 'B2B',
    //                             dataIndex: 'B2B',
    //                             key: 'B2B',
    //                         },
    //                         {
    //                             title: 'B2C',
    //                             dataIndex: 'B2C',
    //                             key: 'B2C',
    //                         },
    //                         {
    //                             title: 'Elux',
    //                             dataIndex: 'Elux',
    //                             key: 'Elux',
    //                         },
    //                     ],
    //                 };

    //                 columnsSet.push(columnItem);
    //             });
    //         }

    //         return columnsSet;
    //     }
    // };

    // const columnDataSet = getTableColumns(data);

    // const getTableSourceData = (data) => {
    //     if (data) {
    //         const { years: columnsData } = data.data;
    //         const sourseData = [];

    //         for (let i = 0; i < columnsData.length; i++) {
    //             for (let j = 0; j < columnsData[i].months.length; j++) {
    //                 const sourceData = {
    //                     key: i + j + 1,
    //                     month: j,
    //                     year: columnsData[i],
    //                     B2B: columnsData[i].months[j].b2b,
    //                     B2C: columnsData[i].months[j].b2c,
    //                     Elux: columnsData[i].months[j].elux,
    //                 };

    //                 sourseData.push(sourceData);
    //             }
    //         }

    //         // if (columnsData.length > 0) {
    //         //     columnsData.map((item, index) => {
    //         //         const sourceData = {
    //         //             key: index + 1,
    //         //             month: 'Jan',
    //         //             B2B: item.months && item.months.map((i) => i.b2b),
    //         //             B2C: item.months && item.months.map((i) => i.b2c),
    //         //             Elux: item.months && item.months.map((i) => i.elux),
    //         //         };

    //         //         sourseData.push(sourceData);
    //         //     });
    //         // }
    //         return sourseData;
    //     }
    // };

    // const tableSrc = getTableSourceData(data);

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
                    item.months.map((m) => {
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
        labels: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec',
        ],
        datasets: chartjsDataSet && chartjsDataSet,
    };

    return isLoading ? (
        <h1>Loading...</h1>
    ) : (
        <>
            <div className="header-wrapper">
                <GraphTableSwitch
                    grapOrTable={graphTableforMonth}
                    handleSwitchChange={handleSwitchChange}
                />
                <DownloadButton />
            </div>
            <div className="graph-overview">
                <h2 className="graph-title">
                    Overview of Events <span>by month</span>
                </h2>
                <LocalFilter
                    requestData={requestData}
                    setRequestData={setRequestData}
                    location="event-by-months-timeline"
                />
            </div>

            {graphTableforMonth === 'graph' ? (
                <Bar options={options} data={graphData} />
            ) : (
                <Table
                    className="custom_footer"
                    pagination={false}
                    columns={columns}
                    dataSource={tableData}
                    bordered
                    size="middle"
                    footer={() => (
                        <Row style={{ justifyContent: 'space-between' }}>
                            <Col
                                span={1}
                                style={{
                                    flexBasis: '10%',
                                    maxWidth: '10%',
                                    padding: '12px 8px',
                                }}
                            >
                                <strong>TOTALS:</strong>
                            </Col>
                            {Object.keys(eventTotals).map((event, index) => (
                                <Col
                                    style={{
                                        flexBasis: '10%',
                                        maxWidth: '10%',
                                        padding: '12px 8px',
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
