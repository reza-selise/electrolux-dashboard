import { createSlice } from '@reduxjs/toolkit';

const years = [];
const currentYear = new Date().getFullYear();
for (let i = 0; i < 5; i += 1) {
    years.push(currentYear - i);
}
const initialState = {
    value: years,
};

export const eventbyCategoryTimelineYearsSlice = createSlice({
    name: 'eventbyCategoryTimelineYears',
    initialState,
    reducers: {
        setEventbyCategoryTimelineYears: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventbyCategoryTimelineYears } = eventbyCategoryTimelineYearsSlice.actions;

export default eventbyCategoryTimelineYearsSlice.reducer;
