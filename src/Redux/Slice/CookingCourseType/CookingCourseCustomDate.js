import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [],
};

export const cookingCourseCustomDateSlice = createSlice({
    name: 'cookingCourseCustomDate',
    initialState,
    reducers: {
        setCookingCourseCustomDate: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setCookingCourseCustomDate } = cookingCourseCustomDateSlice.actions;

export default cookingCourseCustomDateSlice.reducer;
