import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: 'years',
};

export const eventByStatusFilterTypeSlice = createSlice({
    name: 'eventByStatusFilterType',
    initialState,
    reducers: {
        setEventByStatusFilterType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventByStatusFilterType } = eventByStatusFilterTypeSlice.actions;

export default eventByStatusFilterTypeSlice.reducer;
