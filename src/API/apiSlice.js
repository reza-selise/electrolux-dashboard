import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';

const siteURL = window.eluxDashboard.homeUrl;

export const eluxAPI = createApi({
    reducerPath: 'eluxAPI',
    baseQuery: fetchBaseQuery({ baseUrl: `${siteURL}/wp-json/` }),
    endpoints: (builder) => ({
        getGenericComment: builder.query({
            query: () => 'el-dashboard-api/generic-comments',
        }),
    }),
});

export const { useGetGenericCommentQuery } = eluxAPI;
