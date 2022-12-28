import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: 'years',
};

export const eventByLocationFilterTypeSlice = createSlice({
    name: 'eventByLocationFilterType',
    initialState,
    reducers: {
        setEventByLocationFilterType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventByLocationFilterType } = eventByLocationFilterTypeSlice.actions;

export default eventByLocationFilterTypeSlice.reducer;
