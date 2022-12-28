import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [],
};

export const eventByCancellationCustomDateSlice = createSlice({
    name: 'eventByCancellationCustomDate',
    initialState,
    reducers: {
        setEventByCancellationCustomDate: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventByCancellationCustomDate } = eventByCancellationCustomDateSlice.actions;

export default eventByCancellationCustomDateSlice.reducer;
