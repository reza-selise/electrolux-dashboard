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
import { useEventByLocationQuery } from '../../API/apiSlice';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';
import './EventByLocation.scss';

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

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
  const [requestData, setRequestData] = useState('events');
  console.log('re', requestData);

  const [grapOrTableForLocation, setgGrapOrTableForLocation] =
    useState('graph');
  const handleSwitchChange = (e) => {
    setgGrapOrTableForLocation(e.target.value);
    console.log(e.target.value);
  };
  const payload = {
    request_data: 'events',
    filter_type: 'months',
    locations: '459,188',
    request_body: JSON.stringify([
      {
        year: '2022',
        months: '02,08',
      },
      {
        year: '2021',
        months: '03,06',
      },
      {
        year: '2020',
        months: '04,09',
      },
    ]),
  };

  const { data } = useEventByLocationQuery(payload);
  // console.log('data', data);

  const getYears = (data) => {
    return [2022, 2021, 2020];
  };

  const colors = ['#93735a', '#4a2016', '#a6b2a4', '#6b7a66', '#3b4536' ]
  const labels = getYears(data);
  const datasets = labels.map((label , index) => {
    return {
      label: label,
      data:
        (data && data.data.locations.map((location) => location.label)) || 0,
      backgroundColor: colors[index % colors.length],
    };
  });
  // console.log('labels', labels);
  // console.log('datasets', datasets);
  const graphData = {
    labels,
    datasets: datasets,
  };
  return (
    <>
      <div className="header-wrapper">
        <GraphTableSwitch
          grapOrTable={grapOrTableForLocation}
          handleSwitchChange={handleSwitchChange}
        />
        <DownloadButton />
      </div>
      <div className="graph-overview">
        <h2 className="graph-title">
          Overview of Events per location <span>by year</span>
        </h2>
      </div>
      <LocalFilter
        requestData={requestData}
        setRequestData={setRequestData}
        displayClass={'d-block'}
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
