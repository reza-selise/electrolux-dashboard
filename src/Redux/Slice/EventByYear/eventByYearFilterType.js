import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: 'years',
};

export const eventByYearFilterTypeSlice = createSlice({
    name: 'eventByYearFilterType',
    initialState,
    reducers: {
        setEventByYearFilterType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventByYearFilterType } = eventByYearFilterTypeSlice.actions;

export default eventByYearFilterTypeSlice.reducer;
