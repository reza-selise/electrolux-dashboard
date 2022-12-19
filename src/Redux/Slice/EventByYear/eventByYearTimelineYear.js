import { createSlice } from '@reduxjs/toolkit';

const years = [];
const currentYear = new Date().getFullYear();
for (let i = 0; i < 5; i += 1) {
    years.push(currentYear - i);
}
const initialState = {
    value: years,
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
