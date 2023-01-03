import { createSlice } from '@reduxjs/toolkit';

const years = [];
const currentYear = new Date().getFullYear();
for (let i = 0; i < 5; i += 1) {
    years.push(currentYear - i);
}
const initialState = {
    value: years,
};

export const globalFilterTimelineYearsSlice = createSlice({
    name: 'globalFilterTimelineYears',
    initialState,
    reducers: {
        setGlobalFilterTimelineYears: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setGlobalFilterTimelineYears } = globalFilterTimelineYearsSlice.actions;

export default globalFilterTimelineYearsSlice.reducer;
