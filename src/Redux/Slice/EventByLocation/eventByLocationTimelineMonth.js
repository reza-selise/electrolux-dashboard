import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
};

export const eventbyLocationTimelineMonthSlice = createSlice({
    name: 'eventbyLocationTimelineMonth',
    initialState,
    reducers: {
        setEventbyLocationTimelineMonth: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventbyLocationTimelineMonth } = eventbyLocationTimelineMonthSlice.actions;

export default eventbyLocationTimelineMonthSlice.reducer;
