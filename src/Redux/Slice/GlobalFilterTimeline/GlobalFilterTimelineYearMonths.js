import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
};

export const globalFilterTimelineYearMonthsSlice = createSlice({
    name: 'globalFilterTimelineYearMonths',
    initialState,
    reducers: {
        setGlobalFilterTimelineYearMonths: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setGlobalFilterTimelineYearMonths } = globalFilterTimelineYearMonthsSlice.actions;

export default globalFilterTimelineYearMonthsSlice.reducer;
