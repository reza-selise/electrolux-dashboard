import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: '',
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
