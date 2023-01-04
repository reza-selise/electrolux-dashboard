import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [],
};

export const globalFilterTimelineCustomDateSlice = createSlice({
    name: 'globalFilterTimelineCustomDate',
    initialState,
    reducers: {
        setGlobalFilterTimelineCustomDate: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setGlobalFilterTimelineCustomDate } = globalFilterTimelineCustomDateSlice.actions;

export default globalFilterTimelineCustomDateSlice.reducer;
