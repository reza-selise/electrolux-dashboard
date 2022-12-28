import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
};

export const eventByCancellationMonthsSlice = createSlice({
    name: ' eventByCancellationMonths',
    initialState,
    reducers: {
        setEventByCancellationMonths: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventByCancellationMonths } = eventByCancellationMonthsSlice.actions;

export default eventByCancellationMonthsSlice.reducer;
