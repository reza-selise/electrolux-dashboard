import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [],
};

export const eventByStatusCustomDateSlice = createSlice({
    name: 'eventByStatusCustomDate',
    initialState,
    reducers: {
        setEventByStatusCustomDate: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventByStatusCustomDate } = eventByStatusCustomDateSlice.actions;

export default eventByStatusCustomDateSlice.reducer;
