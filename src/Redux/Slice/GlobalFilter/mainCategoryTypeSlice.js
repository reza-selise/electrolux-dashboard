import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [
        'steamdemoProfiline',
        'steamdemoOwners',
        'steamdemoInterestedParties',
        'cookingCourse',
        'eventLocation',
        'diverses',
        'specialOperationsInhouse',
        'electroluxEmployeeTraining',
        'adCustomerEvent',
    ],
};

export const mainCategoryTypeSlice = createSlice({
    name: 'mainCategoryType',
    initialState,
    reducers: {
        setMainCategorytype: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setMainCategorytype } = mainCategoryTypeSlice.actions;

export default mainCategoryTypeSlice.reducer;
