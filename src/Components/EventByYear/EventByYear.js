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
import { useEventByYearQuery } from '../../API/apiSlice';
import { eluxTranslation } from '../../Translation/Translation';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';
import './EventByYear.scss';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

export const options = {
    plugins: {
        title: {
            display: false,
        },
        // legend: {
        //     align: 'start',
        //     height: '16px',
        //     width: '16px',
        // },
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
const { Column } = Table;

function EventByYear() {
    const eventbyYearTimelineYears = useSelector(state => state.eventbyYearTimelineYears.value);
    const eventbyYearTimelineMonth = useSelector(state => state.eventbyYearTimelineMonth.value);
    const eventByYearFilterType = useSelector(state => state.eventByYearFilterType.value);
    const eventbyYearTimelineYearDateRange = useSelector(
        state => state.eventbyYearTimelineYearDateRange.value
    );
    const [requestData, setRequestData] = useState('events');
    const [requestBody, setRequestBody] = useState();

    const [grapOrTableEvntYear, setGrapOrTableEvntYear] = useState('graph');

    useEffect(() => {
        switch (eventByYearFilterType) {
            case 'custom_date_range':
                setRequestBody(eventbyYearTimelineYearDateRange);
                break;
            case 'years':
                setRequestBody(
                    eventbyYearTimelineYears.map(year => ({
                        year: year.toString(),
                        months: eventbyYearTimelineMonth.toString(),
                    }))
                );
                break;
            default:
                console.log('event request data not updated');
        }
    }, [
        eventByYearFilterType,
        eventbyYearTimelineYears,
        eventbyYearTimelineMonth,
        eventbyYearTimelineYearDateRange,
    ]);

    const payload = {
        request_data: requestData,
        filter_type: eventByYearFilterType,
        request_body: JSON.stringify(requestBody),
    };
    const { data, error, isLoading } = useEventByYearQuery(payload);
    const labels =
        data && data.data.years.map(year => year.year)
            ? data.data.years.map(year => year.year)
            : ['2022'];

    const graphData = {
        labels,
        datasets: [
            {
                label: 'ELUX',
                data:
                    data && data.data.years.map(year => year.elux)
                        ? data.data.years.map(year => year.elux)
                        : 0,
                backgroundColor: '#4A2017',
                barThickness: 32,
            },
            {
                label: 'B2B',
                data:
                    data && data.data.years.map(year => year.b2b)
                        ? data.data.years.map(year => year.b2b)
                        : 0,
                backgroundColor: '#937359',
                barThickness: 32,
            },
            {
                label: 'B2C',
                data:
                    data && data.data.years.map(year => year.b2c)
                        ? data.data.years.map(year => year.b2c)
                        : 0,
                backgroundColor: '#D0B993',
                barThickness: 32,
            },
        ],
    };

    const { pleaseWait } = eluxTranslation;

    return (
        <>
            <div className="header-wrapper">
                <GraphTableSwitch
                    identifier={1}
                    grapOrTable={grapOrTableEvntYear}
                    setgGrapOrTable={setGrapOrTableEvntYear}
                    name="event-by-year"
                />
                <DownloadButton identifier={1} />
            </div>
            <div className="graph-overview">
                <h2 className="graph-title">
                    Overview of Events <span>by year</span>
                </h2>
                <LocalFilter
                    requestData={requestData}
                    setRequestData={setRequestData}
                    location="event-by-year-timeline"
                />
            </div>
            {error ? (
                'error'
            ) : isLoading ? (
                pleaseWait
            ) : grapOrTableEvntYear === 'graph' ? (
                <Bar id="event-by-year-graph" options={options} data={graphData} />
            ) : (
                <Table
                    dataSource={data.data.years}
                    pagination={false}
                    className="event-by-year-table"
                >
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

export default EventByYear;
