import { createSlice } from '@reduxjs/toolkit';

const customerOptions = window.eluxDashboard.eventGenericFilterData.customer_types;
customerOptions.map(({ value }) => value);
const initialState = {
    value: customerOptions.map(({ value }) => value),
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
