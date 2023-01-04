import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: 'years',
};

export const globalFilterTimelineFilterTypeSlice = createSlice({
    name: 'globalFilterTimelineFilterType',
    initialState,
    reducers: {
        setGlobalFilterTimelineFilterType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setGlobalFilterTimelineFilterType } = globalFilterTimelineFilterTypeSlice.actions;

export default globalFilterTimelineFilterTypeSlice.reducer;
