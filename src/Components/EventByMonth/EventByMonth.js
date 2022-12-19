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
      {
        year: '2019',
        months: '01,02,03,04,05,06,07,08,09,10,11,12',
      },
      {
        year: '2018',
        months: '01,02,03,04,05,06,07,08,09,10,11,12',
      },
    ]),
  };
  const { data, error, isLoading } = useEventByMonthQuery(payload);

  const chartjsDataSet = [];

  if (data && data.data) {
    const { years: resData } = data.data;

    if (resData) {
      resData.map((item, index) => {
        const singleDataSet = {};
        singleDataSet.label = item.year;

        if (item.months.length > 0) {
          let data = [];
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

    //   {
    //     label: '2022',
    //     data:
    //       data && data.data.years.map((year) => year.elux)
    //         ? data.data.years.map((year) => year.elux)
    //         : 0,
    //     backgroundColor: '#4A2017',
    //   },
    //   {
    //     label: '2021',
    //     data:
    //       data && data.data.years.map((year) => year.b2b)
    //         ? data.data.years.map((year) => year.b2b)
    //         : 0,
    //     backgroundColor: '#937359',
    //   },
    //   {
    //     label: '2020',
    //     data:
    //       data && data.data.years.map((year) => year.b2c)
    //         ? data.data.years.map((year) => year.b2c)
    //         : 0,
    //     backgroundColor: '#D0B993',
    //   },
    //   {
    //     label: '2019',
    //     data:
    //       data && data.data.years.map((year) => year.b2c)
    //         ? data.data.years.map((year) => year.b2c)
    //         : 0,
    //     backgroundColor: '#D0B993',
    //   },
    //   {
    //     label: '2018',
    //     data:
    //       data && data.data.years.map((year) => year.b2c)
    //         ? data.data.years.map((year) => year.b2c)
    //         : 0,
    //     backgroundColor: '#D0B993',
    //   },
    // ],
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
