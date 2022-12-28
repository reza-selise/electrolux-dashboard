import { Select } from 'antd';
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
import { useCookingCourseTypeQuery } from '../../API/apiSlice';
import DownloadButton from '../DownloadButton/DownloadButton';
import GraphTableSwitch from '../GraphTableSwitch/GraphTableSwitch';
import LocalFilter from '../LocalFilter/LocalFilter';
import './CookingCourseType.scss';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);
const options = {
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

function TableView({ data }) {
    const columns = data.labels.map(label => ({
        title: label,
        key: label,
    }));
    const dataSource = data.rows;

    return (
        <table className="cooking-course-table">
            <thead>
                <tr>
                    {columns.map(item => (
                        <th key={item.key}>{item.title} </th>
                    ))}
                </tr>
            </thead>
            <tbody>
                {dataSource.map((item, index) => (
                    <tr key={index}>
                        {item.map((data, index) => (
                            <td key={index}>{data} </td>
                        ))}
                    </tr>
                ))}
            </tbody>
        </table>
    );
}

function CookingCourseType() {
    const [requestData, setRequestData] = useState('events');
    const [grapTableCookingCourse, setGrapTableCookingCourse] = useState('graph');
    const [productStatus, setProductStatus] = useState('Took Place');
    const [payload, setPayload] = useState();
    const cookingCourseFilterType = useSelector(state => state.cookingCourseFilterType.value);
    const cookingCourseYears = useSelector(state => state.cookingCourseYears.value);
    const cookingCourseMonths = useSelector(state => state.cookingCourseMonths.value);
    const cookingCourseYearMonths = useSelector(state => state.cookingCourseYearMonths.value);
    const cookingCourseCustomDate = useSelector(state => state.cookingCourseCustomDate.value);

    // console.log('cookingCourseCustomDate', cookingCourseCustomDate);
    useEffect(() => {
        switch (cookingCourseFilterType) {
            case 'years':
                setPayload({
                    type: requestData,
                    timeline_type: 'years',
                    timeline_filter: cookingCourseYears,
                    filter_key_value: {
                        product_status: productStatus,
                    },
                    year_months: cookingCourseYearMonths,
                });
                break;

            case 'months':
                setPayload({
                    type: requestData,
                    timeline_type: 'months',
                    timeline_filter: cookingCourseMonths,
                    filter_key_value: {
                        product_status: productStatus,
                    },
                });
                break;
            case 'custom_date_range':
                setPayload({
                    type: requestData,
                    timeline_type: 'custom_date_range',
                    timeline_filter: cookingCourseCustomDate,
                    filter_key_value: {
                        product_status: productStatus,
                    },
                });
                break;
            case 'custom_time_frame':
                setPayload({
                    type: requestData,
                    timeline_type: 'custom_time_frame',
                    timeline_filter: cookingCourseMonths,
                    filter_key_value: {
                        product_status: productStatus,
                    },
                });
                break;

            default:
                setPayload({
                    type: 'events',
                    timeline_type: 'years',
                    timeline_filter: ['2022', '2021', '2020', '2019', '2018'],
                    filter_key_value: {},
                });
                console.log('Cooking course type default payload');
        }
    }, [
        requestData,
        cookingCourseFilterType,
        cookingCourseYears,
        cookingCourseMonths,
        productStatus,
        cookingCourseYearMonths,
        cookingCourseCustomDate,
    ]);

    const { data, isLoading } = useCookingCourseTypeQuery(payload);

    const handleProductStatusChange = value => {
        setProductStatus(value);
    };
    const colors = ['#937359', '#4A2017', '#A6B2A4', '#6B7A66', '#3B4536'];

    const graphData =
        isLoading === false && data.status === true
            ? {
                  labels: data.data.labels ? data.data.labels : [],
                  datasets: data.data.datasets.map((dataset, index) => ({
                      ...dataset,
                      backgroundColor: colors[index % colors.length],
                      // barThickness: 24,
                      // borderDash: [],
                      //   borderDashOffset: 0.0,
                  })),
              }
            : [];

    return (
        <div className="cooking-course-type-wrapper">
            <div className="header-wrapper">
                <GraphTableSwitch
                    grapOrTable={grapTableCookingCourse}
                    setgGrapOrTable={setGrapTableCookingCourse}
                    name="cooking-course-type"
                />
                <DownloadButton identifier={7} />
            </div>
            <h2 className="graph-title">Overview of Cooking Course Type</h2>
            <div className="graph-overview">
                <LocalFilter
                    showBoth="true"
                    requestData={requestData}
                    setRequestData={setRequestData}
                    location="cooking-course-type-timeline"
                />
                <Select
                    defaultValue={productStatus}
                    style={{ width: 120 }}
                    onChange={handleProductStatusChange}
                    options={[
                        {
                            value: 'Took Place',
                            label: 'Taken Place',
                        },
                        {
                            value: 'Planned',
                            label: 'Planned',
                        },
                        {
                            value: 'Reserved',
                            label: 'Reserved',
                        },
                    ]}
                />
            </div>

            {data && data.status ? (
                grapTableCookingCourse === 'graph' ? (
                    isLoading === false ? (
                        <Bar options={options} data={graphData} />
                    ) : (
                        'Loading'
                    )
                ) : (
                    data && <TableView data={data && data.table_data} />
                )
            ) : (
                'Nothng Found'
            )}
        </div>
    );
}

export default CookingCourseType;
