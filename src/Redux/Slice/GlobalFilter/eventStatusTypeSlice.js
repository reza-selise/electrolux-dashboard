import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [
        'planned',
        'canceled',
        'takenPlace'
    ],
};

export const eventStatusTypeSlice = createSlice({
    name: 'eventStatusType',
    initialState,
    reducers: {
        setEventStatusType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventStatusType } = eventStatusTypeSlice.actions;

export default eventStatusTypeSlice.reducer;
