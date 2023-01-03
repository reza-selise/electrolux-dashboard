import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';

const siteURL = window.eluxDashboard.homeUrl;

export const eluxAPI = createApi({
    reducerPath: 'eluxAPI',
    baseQuery: fetchBaseQuery({ baseUrl: `${siteURL}/wp-json/` }),
    tagTypes: ['GenericComment', 'IndividualComment'],
    endpoints: builder => ({
        insetGenericComment: builder.mutation({
            query: payload => ({
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
            query: payload => ({
                url: 'el-dashboard-api/generic-comments',
                method: 'DELETE',
                body: payload,
            }),
            invalidatesTags: ['GenericComment'],
        }),
        updateGenericComment: builder.mutation({
            query: payload => ({
                url: 'el-dashboard-api/generic-comments',
                method: 'PUT',
                body: payload,
            }),
            invalidatesTags: ['GenericComment'],
        }),
        eventByYear: builder.query({
            query: payload => ({
                url: `elux-dashboard/v1/events-by-year?${new URLSearchParams(payload)}`,
                method: 'GET',
            }),
        }),
        eventByLocation: builder.query({
            query: payload => ({
                url: `elux-dashboard/v1/events-by-location?${new URLSearchParams(payload)}`,
                method: 'GET',
            }),
        }),
        eventByMonths: builder.query({
            query: payload => ({
                url: `elux-dashboard/v1/events-by-month?${new URLSearchParams(payload)}`,
                method: 'GET',
            }),
        }),
        eventByCategory: builder.query({
            query: payload => ({
                url: 'elux-dashboard/v1/events-by-category',
                method: 'POST',
                body: payload,
            }),
        }),
        eventByStatus: builder.query({
            query: payload => ({
                url: `elux-dashboard/v1/events-by-status?${new URLSearchParams(payload)}`,
                method: 'GET',
            }),
        }),
        cookingCourseType: builder.query({
            query: payload => ({
                url: 'elux-dashboard/v1/events-by-cooking-course-type',
                method: 'POST',
                body: payload,
            }),
        }),
        eventByCancellation: builder.query({
            query: payload => ({
                url: `elux-dashboard/v1/events-by-cancellation`,
                method: 'POST',
                body: payload,
            }),
        }),
        eventPerSalesPerson: builder.query({
            query: payload => ({
                url: `elux-dashboard/v1/events-by-sale-person`,
                method: 'POST',
                body: payload,
            }),
        }),
        insetIndividualComment: builder.mutation({
            query: payload => ({
                url: 'elux-dashboard/v1/section-comments',
                method: 'POST',
                body: payload,
            }),
            invalidatesTags: ['IndividualComment'],
        }),
        getIndividualComment: builder.query({
            query: () => 'elux-dashboard/v1/generic-comments',
            providesTags: ['IndividualComment'],
        }),
    }),
});

export const {
    useGetGenericCommentQuery,
    useInsetGenericCommentMutation,
    useDeleteGenericCommentMutation,
    useUpdateGenericCommentMutation,
    useEventByYearQuery,
    useEventByMonthsQuery,
    useEventByLocationQuery,
    useEventByCategoryQuery,
    useCookingCourseTypeQuery,
    useEventByStatusQuery,
    useEventByCancellationQuery,
    useEventPerSalesPersonQuery,
    useInsetIndividualCommentMutation,
    useGetIndividualCommentQuery,
} = eluxAPI;
