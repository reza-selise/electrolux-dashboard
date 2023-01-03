import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';

const siteURL = window.eluxDashboard.homeUrl;

export const eluxAPI = createApi({
    reducerPath: 'eluxAPI',
    baseQuery: fetchBaseQuery({ baseUrl: `${siteURL}/wp-json/elux-dashboard/v1` }),
    tagTypes: ['GenericComment', 'IndividualComment'],
    endpoints: builder => ({
        insetGenericComment: builder.mutation({
            query: payload => ({
                url: '/generic-comments',
                method: 'POST',
                body: payload,
            }),
            invalidatesTags: ['GenericComment'],
        }),
        getGenericComment: builder.query({
            query: () => '/generic-comments',
            providesTags: ['GenericComment'],
        }),
        deleteGenericComment: builder.mutation({
            query: payload => ({
                url: '/generic-comments',
                method: 'DELETE',
                body: payload,
            }),
            invalidatesTags: ['GenericComment'],
        }),
        updateGenericComment: builder.mutation({
            query: payload => ({
                url: '/generic-comments',
                method: 'PUT',
                body: payload,
            }),
            invalidatesTags: ['GenericComment'],
        }),
        eventByYear: builder.query({
            query: payload => ({
                url: `/events-by-year?${new URLSearchParams(payload)}`,
                method: 'GET',
            }),
        }),
        eventByLocation: builder.query({
            query: payload => ({
                url: `/events-by-location?${new URLSearchParams(payload)}`,
                method: 'GET',
            }),
        }),
        eventByMonths: builder.query({
            query: payload => ({
                url: `/events-by-month?${new URLSearchParams(payload)}`,
                method: 'GET',
            }),
        }),
        eventByCategory: builder.query({
            query: payload => ({
                url: '/events-by-category',
                method: 'POST',
                body: payload,
            }),
        }),
        eventByStatus: builder.query({
            query: payload => ({
                url: `/events-by-status?${new URLSearchParams(payload)}`,
                method: 'GET',
            }),
        }),
        cookingCourseType: builder.query({
            query: payload => ({
                url: '/events-by-cooking-course-type',
                method: 'POST',
                body: payload,
            }),
        }),
        eventByCancellation: builder.query({
            query: payload => ({
                url: `/events-by-cancellation`,
                method: 'POST',
                body: payload,
            }),
        }),
        eventPerSalesPerson: builder.query({
            query: payload => ({
                url: `/events-by-sale-person`,
                method: 'POST',
                body: payload,
            }),
        }),
        insetIndividualComment: builder.mutation({
            query: payload => ({
                url: '/section-comments',
                method: 'POST',
                body: payload,
            }),
            invalidatesTags: ['IndividualComment'],
        }),
        getIndividualComment: builder.query({
            query: () => '/generic-comments',
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
