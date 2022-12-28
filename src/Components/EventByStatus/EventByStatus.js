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
import { useEventByStatusQuery } from '../../API/apiSlice';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);
const { Column } = Table;

function Graph({ data }) {
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
    const labels =
        data && data.years.map(year => year.year) ? data.years.map(year => year.year) : ['2022'];
    const graphData = {
        labels,
        datasets: [
            {
                label: 'Planned',
                data:
                    data && data.years.map(year => year.planned)
                        ? data.years.map(year => year.planned)
                        : 0,
                backgroundColor: '#2B2936',
            },
            {
                label: 'Cancelled',
                data:
                    data && data.years.map(year => year.cancelled)
                        ? data.years.map(year => year.cancelled)
                        : 0,
                backgroundColor: '#787386',
                // barThickness: 32,
            },
            {
                label: 'Taken Place',
                data:
                    data && data.years.map(year => year.taken_place)
                        ? data.years.map(year => year.taken_place)
                        : 0,
                backgroundColor: '#B0ABBA',
                // barThickness: 32,
            },
        ],
    };
    return <Bar id="event-by-year-graph" options={BarOptions} data={graphData} />;
}

function TableView({ data }) {
    return (
        <Table dataSource={data.years} pagination={false} className="event-by-status-table">
            <Column title="Year" dataIndex="year" key="year" />
            <Column title="Planned" dataIndex="planned" key="planned" />
            <Column title="Cancelled" dataIndex="cancelled" key="cancelled" />
            <Column title="Taken Place" dataIndex="taken_place" key="taken_place" />
            <Column title="Total" dataIndex="total" key="total" />
        </Table>
    );
}

function EventByStatus() {
    const [grapOrTableEvntStatus, setGrapOrTableEvntStatus] = useState('graph');
    const [payload, setPayload] = useState({
        request_data: 'events',
        filter_type: 'years',
        request_body: JSON.stringify([
            {
                year: '2022',
                months: '01,02,03,04,05,06,07,08,09,10,11,12',
            },
        ]),
    });
    const { data, error, isLoading } = useEventByStatusQuery(payload);

    const eventByStatusFilterType = useSelector(state => state.eventByStatusFilterType.value);
    const eventByStatusYears = useSelector(state => state.eventByStatusYears.value);
    const eventByStatusYearMonth = useSelector(state => state.eventByStatusYearMonth.value);
    const eventByStatusMonths = useSelector(state => state.eventByStatusMonths.value);
    const eventByStatusCustomDate = useSelector(state => state.eventByStatusCustomDate.value);

    useEffect(() => {
        const years = [];
        const currentYear = new Date().getFullYear();
        for (let i = 0; i < 5; i += 1) {
            years.push(currentYear - i);
        }
        switch (eventByStatusFilterType) {
            case 'years':
                setPayload({
                    request_data: 'events',
                    filter_type: 'years',
                    request_body: JSON.stringify(
                        eventByStatusYears.map(year => ({
                            year: year.toString(),
                            months: eventByStatusYearMonth.toString(),
                        }))
                    ),
                });
                break;

            case 'months':
                setPayload({
                    request_data: 'events',
                    filter_type: 'years',
                    request_body: JSON.stringify(
                        years.map(year => ({
                            year: year.toString(),
                            months: eventByStatusMonths.toString(),
                        }))
                    ),
                });
                break;
            case 'custom_date_range':
                setPayload({
                    request_data: 'events',
                    filter_type: 'years',
                    request_body: JSON.stringify(eventByStatusCustomDate),
                });
                break;
            case 'custom_time_frame':
                setPayload();
                break;

            default:
                setPayload();
                console.log('Cooking course type default payload');
        }
    }, [
        eventByStatusYears,
        eventByStatusYearMonth,
        eventByStatusFilterType,
        eventByStatusCustomDate,
    ]);

    return (
        <>
            <div className="header-wrapper">
                <GraphTableSwitch
                    identifier={1}
                    grapOrTable={grapOrTableEvntStatus}
                    setgGrapOrTable={setGrapOrTableEvntStatus}
                    name="event-by-status"
                />
                <DownloadButton identifier={5} />
            </div>
            <div className="graph-overview">
                <h2 className="graph-title">Overview of Events by Status</h2>
                <LocalFilter showBoth="false" location="event-by-status-timeline" />
            </div>
            {error ? (
                'Error MSG'
            ) : isLoading ? (
                'Loading'
            ) : grapOrTableEvntStatus === 'graph' ? (
                <Graph data={data.data} />
            ) : (
                <TableView data={data.data} />
            )}
        </>
    );
}

export default EventByStatus;
