import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
};

export const eventbyYearTimelineMonthSlice = createSlice({
    name: 'eventbyYearTimelineMonth',
    initialState,
    reducers: {
        setEventbyYearTimelineMonth: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventbyYearTimelineMonth } = eventbyYearTimelineMonthSlice.actions;

export default eventbyYearTimelineMonthSlice.reducer;
