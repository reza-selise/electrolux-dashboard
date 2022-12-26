import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: '',
};

export const eventbyYearTimelineYearDateRangeSlice = createSlice({
    name: 'eventbyYearTimelineYearDateRange',
    initialState,
    reducers: {
        setEventbyYearTimelineYearDateRange: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventbyYearTimelineYearDateRange } =
    eventbyYearTimelineYearDateRangeSlice.actions;

export default eventbyYearTimelineYearDateRangeSlice.reducer;
