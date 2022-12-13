import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';

const siteURL = window.eluxDashboard.homeUrl;

export const eluxAPI = createApi({
    reducerPath: 'eluxAPI',
    baseQuery: fetchBaseQuery({ baseUrl: `${siteURL}/wp-json/` }),
    tagTypes: ['GenericComment'],
    endpoints: (builder) => ({
        insetGenericComment: builder.mutation({
            query: (payload) => ({
                url: 'el-dashboard-api/generic-comments',
                method: 'POST',
                body: payload,
            }),
            invalidatesTags: ['GenericComment'],
        }),
        getGenericComment: builder.query({
            query: () => 'el-dashboard-api/generic-comments',
            providesTags: ['GenericComment'],
        }),
        deleteGenericComment: builder.mutation({
            query: (payload) => ({
                url: 'el-dashboard-api/generic-comments',
                method: 'DELETE',
                body: payload,
            }),
            invalidatesTags: ['GenericComment'],
        }),
        updateGenericComment: builder.mutation({
            query: (payload) => ({
                url: 'el-dashboard-api/generic-comments',
                method: 'PUT',
                body: payload,
            }),
            invalidatesTags: ['GenericComment'],
        }),
        eventByYear: builder.query({
            query: (payload) => ({
                url: `elux-dashboard/v1/events-by-year?${new URLSearchParams(payload)}`,
                method: 'GET',
            }),
        }),
    }),
});

export const {
    useGetGenericCommentQuery,
    useInsetGenericCommentMutation,
    useDeleteGenericCommentMutation,
    useUpdateGenericCommentMutation,
    useEventByYearQuery,
} = eluxAPI;
