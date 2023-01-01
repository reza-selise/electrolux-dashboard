import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: 'years',
};

export const eventPerSalesPersonFilterTypeSlice = createSlice({
    name: 'eventPerSalesPersonFilterType',
    initialState,
    reducers: {
        setEventPerSalesPersonFilterType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventPerSalesPersonFilterType } = eventPerSalesPersonFilterTypeSlice.actions;

export default eventPerSalesPersonFilterTypeSlice.reducer;
