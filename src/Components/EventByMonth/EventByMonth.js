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

    // data for table
    const getTableColumns = (data) => {
        if (data) {
            const { years: columnsData } = data.data;
            const columnsSet = [
                {
                    title: 'Month',
                    dataIndex: 'month',
                    key: 'month',
                    render: (text) => <p>{text}</p>,
                },
            ];

            if (columnsData.length > 0) {
                columnsData.map((item) => {
                    const columnItem = {
                        title: item.year,
                        children: [
                            {
                                title: 'B2B',
                                dataIndex: 'B2B',
                                key: 'B2B',
                            },
                            {
                                title: 'B2C',
                                dataIndex: 'B2C',
                                key: 'B2C',
                            },
                            {
                                title: 'Elux',
                                dataIndex: 'Elux',
                                key: 'Elux',
                            },
                        ],
                    };

                    columnsSet.push(columnItem);
                });
            }

            return columnsSet;
        }
    };

    const columnDataSet = getTableColumns(data);

    const getTableSourceData = (data) => {
        if (data) {
            const { years: columnsData } = data.data;
            const sourseData = [];

            for (let i = 0; i < columnsData.length; i++) {
                for (let j = 0; j < columnsData[i].months.length; j++) {
                    const sourceData = {
                        key: i + j + 1,
                        month: j,
                        B2B: columnsData[i].months[j].b2b,
                        B2C: columnsData[i].months[j].b2c,
                        Elux: columnsData[i].months[j].elux,
                    };

                    sourseData.push(sourceData);
                }
            }

            // if (columnsData.length > 0) {
            //     columnsData.map((item, index) => {
            //         const sourceData = {
            //             key: index + 1,
            //             month: 'Jan',
            //             B2B: item.months && item.months.map((i) => i.b2b),
            //             B2C: item.months && item.months.map((i) => i.b2c),
            //             Elux: item.months && item.months.map((i) => i.elux),
            //         };

            //         sourseData.push(sourceData);
            //     });
            // }
            return sourseData;
        }
    };

    const tableSrc = getTableSourceData(data);

    // data for graph
    const chartjsDataSet = [];

    if (data && data.data) {
        const { years: resData } = data.data;

        if (resData) {
            resData.map((item) => {
                const singleDataSet = {};
                singleDataSet.label = item.year;

                if (item.months.length > 0) {
                    const data = [];
                    item.months.map((m) => {
                        data.push(m.total);
                    });
                    singleDataSet.data = data;
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
                <Table dataSource={tableSrc} columns={columnDataSet} />
            )}
        </>
    );
}

export default EventByMonth;
