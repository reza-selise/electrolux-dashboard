import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [{ start: null, end: null }],
};

export const eventbyLocationimelineYearDateRangeSlice = createSlice({
    name: 'eventbyLocationimelineYearDateRange',
    initialState,
    reducers: {
        seteventbyLocationimelineYearDateRange: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { seteventbyLocationimelineYearDateRange } =
    eventbyLocationimelineYearDateRangeSlice.actions;

export default eventbyLocationimelineYearDateRangeSlice.reducer;
