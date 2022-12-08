import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';

export const eluxAPI = createApi({
    reducerPath: 'eluxAPI',
    baseQuery: fetchBaseQuery({ baseUrl: 'https://pokeapi.co/api/v2/' }),
    endpoints: (builder) => ({
        getEventByDate: builder.query({
            query: (name) => `pokemon/${name}`,
        }),
    }),
});

export const { useGetEventByDateQuery } = eluxAPI;
