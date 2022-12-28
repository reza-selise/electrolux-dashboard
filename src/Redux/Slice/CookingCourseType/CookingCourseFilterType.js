import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: 'years',
};

export const cookingCourseFilterTypeSlice = createSlice({
    name: 'cookingCourseFilterType',
    initialState,
    reducers: {
        setCookingCourseFilterType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setCookingCourseFilterType } = cookingCourseFilterTypeSlice.actions;

export default cookingCourseFilterTypeSlice.reducer;
