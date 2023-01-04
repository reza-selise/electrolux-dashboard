import { Table } from 'antd';
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

const { Column } = Table;

function GraphView({ data }) {
    const BarOptions = {
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
        barPercentage: 1,
    };
    let labels = [];

    let graphData = {};
    try {
        labels = data.status !== 403 ? data.years.map(year => year.year) : ['2022'];

        graphData = {
            labels,
            datasets: [
                {
                    label: 'ELUX',
                    data: data.status !== 403 ? data.years.map(year => year.elux) : 0,
                    backgroundColor: '#4A2017',
                },
                {
                    label: 'B2B',
                    data: data.status !== 403 ? data.years.map(year => year.b2b) : 0,
                    backgroundColor: '#937359',
                    // barThickness: 32,
                },
                {
                    label: 'B2C',
                    data: data.status !== 403 ? data.years.map(year => year.b2c) : 0,
                    backgroundColor: '#D0B993',
                    // barThickness: 32,
                },
            ],
        };
    } catch (e) {
        console.log('evnt graph view error', e);
    }

    return <Bar id="event-by-year-graph" options={BarOptions} data={graphData} />;
}

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
        const years = [];
        const currentYear = new Date().getFullYear();
        for (let i = 0; i < 5; i += 1) {
            years.push(currentYear - i);
        }
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
            case 'months':
                setRequestBody(
                    years.map(year => ({
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
        customer_type: 'all',
        event_status: 'planned',
    };
    const { data, error, isLoading } = useEventByYearQuery(payload);

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
                <DownloadButton
                    identifier={1}
                    location="event-by-year-comment"
                    graphID="event-by-year-graph"
                />
            </div>
            <div className="graph-overview">
                <h2 className="graph-title">
                    Overview of Events <span>by year</span>
                </h2>
                <LocalFilter
                    showBoth="true"
                    requestData={requestData}
                    setRequestData={setRequestData}
                    location="event-by-year-timeline"
                />
            </div>
            {error ? (
                'Error'
            ) : isLoading ? (
                'Loading'
            ) : (
                <>
                    <GraphView
                        data={data.data}
                        style={{ display: grapOrTableEvntYear === 'graph' ? 'block' : 'none' }}
                    />
                    <Table
                        style={{ display: grapOrTableEvntYear === 'table' ? 'block' : 'none' }}
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
                </>
            )}
        </>
    );
}

export default EventByYear;
