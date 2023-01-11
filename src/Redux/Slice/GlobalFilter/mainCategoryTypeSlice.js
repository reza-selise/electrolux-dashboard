import { createSlice } from '@reduxjs/toolkit';

const defaultMainCategory = window.eluxDashboard.eventGenericFilterData.categories.map(
    ({ id }) => id
);

const initialState = {
    value: defaultMainCategory,
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
