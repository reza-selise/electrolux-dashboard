import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [],
};

export const eventbyYearTimelineYearsSlice = createSlice({
    name: 'eventbyYearTimelineYears',
    initialState,
    reducers: {
        setEventbyYearTimelineYears: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventbyYearTimelineYears } = eventbyYearTimelineYearsSlice.actions;

export default eventbyYearTimelineYearsSlice.reducer;
