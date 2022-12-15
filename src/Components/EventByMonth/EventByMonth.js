import { Table } from 'antd';
import {
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  Title,
  Tooltip,
} from 'chart.js';
import React, { useState } from 'react';
// eslint-disable-next-line import/no-unresolved
import { Bar } from 'react-chartjs-2';
import { useEventByMonthQuery } from '../../API/apiSlice';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';
import './EventByMonth.scss';

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
      beginAtZero: true,
    },
  },
};
const { Column } = Table;

function EventByMonth() {
  const [requestData, setRequestData] = useState('events');
  console.log('re', requestData);

  const [graphTableforMonth, setgGrapOrTableForMonth] = useState('graph');
  const handleSwitchChange = (e) => {
    setgGrapOrTableForMonth(e.target.value);
    console.log(e.target.value);
  };
  const payload = {
    request_data: 'events',
    filter_type: 'years',
    request_body: JSON.stringify([
      {
        year: '2022',
        months: '09,10,11',
      },
    ]),
  };
  const { data } = useEventByMonthQuery(payload);
  const labels =
    data && data.data.years.map((year) => year.year)
      ? data.data.years.map((year) => year.year)
      : [
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
  const graphData = {
    labels,
    datasets: [
      {
        label: '2018',
        data:
          data && data.data.years.map((year) => year.elux)
            ? data.data.years.map((year) => year.elux)
            : 0,
        backgroundColor: '#001C3F',
      },
      {
        label: '2019',
        data:
          data && data.data.years.map((year) => year.b2b)
            ? data.data.years.map((year) => year.b2b)
            : 0,
        backgroundColor: '#797285',
      },
      {
        label: '2020',
        data:
          data && data.data.years.map((year) => year.b2c)
            ? data.data.years.map((year) => year.b2c)
            : 0,
        backgroundColor: '#79899B',
      },
      {
        label: '2021',
        data:
          data && data.data.years.map((year) => year.b2c)
            ? data.data.years.map((year) => year.b2c)
            : 0,
        backgroundColor: '#D2BA96',
      },
      {
        label: '2022',
        data:
          data && data.data.years.map((year) => year.b2c)
            ? data.data.years.map((year) => year.b2c)
            : 0,
        backgroundColor: '#697B68',
      },
    ],
  };
  return (
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
      </div>
      <LocalFilter
        requestData={requestData}
        setRequestData={setRequestData}
        displayClass={'d-block'}
      />
      {graphTableforMonth === 'graph' ? (
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

export default EventByMonth;
