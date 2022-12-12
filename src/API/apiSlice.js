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
    }),
});

export const {
    useGetGenericCommentQuery,
    useInsetGenericCommentMutation,
    useDeleteGenericCommentMutation,
} = eluxAPI;
