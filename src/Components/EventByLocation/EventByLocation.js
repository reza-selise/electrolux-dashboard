import {Table} from 'antd';
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    Tooltip
} from 'chart.js';
import React,{useState} from 'react';
import {useSelector} from 'react-redux';
// eslint-disable-next-line import/no-unresolved
import {Bar} from 'react-chartjs-2';
import {useEventByLocationQuery} from '../../API/apiSlice';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';
import './EventByLocation.scss';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

export const options = {
    plugins: {
        title: {
            display: false,
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

function EventByLocation() {
    const eventByLocationFilterType = useSelector(state => state.eventByLocationFilterType.value);
    const eventByLocationTimelineYears = useSelector(
        state => state.eventByLocationTimelineYears.value
    );
    const eventByLocationTimelineMonths = useSelector(
        state => state.eventByLocationTimelineMonths.value
    );

    const [requestDataForLocation, setRequestDataForLocation] = useState('events');
    console.log('re', requestDataForLocation);

    const [grapOrTableForLocation, setgGrapOrTableForLocation] = useState('graph');
    const handleSwitchChange = e => {
        setgGrapOrTableForLocation(e.target.value);
        console.log(e.target.value);
    };
    const request_body = eventByLocationTimelineYears.map(year => ({
        year: year.toString(),
        months: eventByLocationTimelineMonths.toString(),
    }));
    const payload = {
        request_data: requestDataForLocation,
        filter_type: eventByLocationFilterType,
        locations: '191,188',
        request_body: JSON.stringify(
            request_body
            // {
            //     year: '2022',
            //     months: '02,08,12',
            // },
            // {
            //     year: '2021',
            //     months: '02,08,12',
            // },
            // {
            //     year: '2020',
            //     months: '02,08,12',
            // },
            // {
            //     year: '2019',
            //     months: '02,08,12',
            // },
            // {
            //     year: '2018',
            //     months: '02,08,12',
            // },
        ),
    };
    const { data } = useEventByLocationQuery(payload);
    console.log('data', data);

    const getYears = () => ['2022', '2021', '2020', '2019', '2018'];
    const getLocations = () => ['191', '188', '183', '512', '507'];
    const getDataFromLocation = (location, locationYear) =>
        getYears().map(year => locationYear === year && location[year]);

    const colors = ['#93735a', '#4a2016', '#a6b2a4', '#6b7a66', '#3b4536'];
    const labels = getLocations();
    const datasets = getYears().map((year, index) => ({
        label: year,
        data: data && data.data.locations.map(location => getDataFromLocation(location, year)),
        backgroundColor: colors[index % colors.length],
    }));
    // console.log('labels', labels);
    console.log('datasets', datasets);

    const graphData = {
        labels,
        datasets,
    };

    console.log('dataset event per location ', graphData);
    return (
        <>
            <div className="header-wrapper">
                <GraphTableSwitch
                    grapOrTable={grapOrTableForLocation}
                    handleSwitchChange={handleSwitchChange}
                    name="event-by-location"
                />
                <DownloadButton />
            </div>
            <div className="graph-overview">
                <h2 className="graph-title">
                    Overview of Events per location <span>by year</span>
                </h2>
            </div>
            <LocalFilter
                requestData={requestDataForLocation}
                setRequestData={setRequestDataForLocation}
            />
            {grapOrTableForLocation === 'graph' ? (
                <Bar options={options} data={graphData} />
            ) : (
                <Table dataSource={data.data.years}>
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

export default EventByLocation;
