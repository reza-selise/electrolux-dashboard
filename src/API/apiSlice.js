import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';

const siteURL = window.eluxDashboard.homeUrl;

export const eluxAPI = createApi({
    reducerPath: 'eluxAPI',
    baseQuery: fetchBaseQuery({ baseUrl: siteURL }),
    endpoints: (builder) => ({
        getEventByDate: builder.query({
            query: () => 'el-dashboard-api/generic-comments ',
        }),
    }),
});

export const { useGetEventByDateQuery } = eluxAPI;
