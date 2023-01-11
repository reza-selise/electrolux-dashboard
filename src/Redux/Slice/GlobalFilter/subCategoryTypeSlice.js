import { createSlice } from '@reduxjs/toolkit';

const subCategory = window.eluxDashboard.eventGenericFilterData.categories.reduce(
    (subCat, category) => {
        subCat.push(...category.sub_category);
        return subCat;
    },
    []
);

const initialState = {
    value: subCategory.map(({ id }) => id),
};

export const subCategoryTypeSlice = createSlice({
    name: 'subCategoryType',
    initialState,
    reducers: {
        setSubCategoryType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setSubCategoryType } = subCategoryTypeSlice.actions;

export default subCategoryTypeSlice.reducer;
