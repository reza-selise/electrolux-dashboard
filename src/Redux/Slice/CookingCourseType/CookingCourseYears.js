import { createSlice } from '@reduxjs/toolkit';

const years = [];
const currentYear = new Date().getFullYear();
for (let i = 0; i < 5; i += 1) {
    years.push(currentYear - i);
}
const initialState = {
    value: years,
};

export const cookingCourseYearsSlice = createSlice({
    name: 'cookingCourseYears',
    initialState,
    reducers: {
        setCookingCourseYears: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setCookingCourseYears } = cookingCourseYearsSlice.actions;

export default cookingCourseYearsSlice.reducer;
