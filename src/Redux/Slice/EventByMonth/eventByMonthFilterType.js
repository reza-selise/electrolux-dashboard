import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: 'years',
};

export const eventByMonthFilterTypeSlice = createSlice({
    name: 'eventByCategoryFilterType',
    initialState,
    reducers: {
        setEventByMonthFilterType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventByMonthFilterType } = eventByMonthFilterTypeSlice.actions;

export default eventByMonthFilterTypeSlice.reducer;
