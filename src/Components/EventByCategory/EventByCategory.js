import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    Tooltip
} from 'chart.js';
import React from 'react';
import { Bar } from 'react-chartjs-2';
import { useEventByCategoryQuery } from '../../API/apiSlice';

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
function EventByCategory() {
    const payload = {
        type: 'participant',
        timeline_type: 'year',
        timeline_filter: ['2022', '2024', '2020'],
        filter_key_value: {},
    };
    const { data } = useEventByCategoryQuery(payload);
    console.log('category', data);
    const labels = data && data.data.labels;
    const graphData = {
        labels,
        datasets: data && data.data.dataset,
    };
    return <div>{data && <Bar options={options} data={graphData} />}</div>;
}

export default EventByCategory;
