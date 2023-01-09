import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: 'all',
};

export const customerTypeSlice = createSlice({
    name: 'customerType',
    initialState,
    reducers: {
        setCustomerType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setCustomerType } = customerTypeSlice.actions;

export default customerTypeSlice.reducer;
