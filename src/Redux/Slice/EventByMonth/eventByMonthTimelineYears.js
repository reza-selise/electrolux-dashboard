import { createSlice } from '@reduxjs/toolkit';

const years = [];
const currentYear = new Date().getFullYear();
for (let i = 0; i < 5; i += 1) {
    years.push(currentYear - i);
}
const initialState = {
    value: years,
};

export const eventbyMonthTimelineYearsSlice = createSlice({
    name: 'eventbyMonthTimelineYears',
    initialState,
    reducers: {
        setEventbyMonthTimelineYears: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventbyMonthTimelineYears } = eventbyMonthTimelineYearsSlice.actions;

export default eventbyMonthTimelineYearsSlice.reducer;
