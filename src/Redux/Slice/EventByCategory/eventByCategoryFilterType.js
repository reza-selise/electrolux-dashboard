import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: 'years',
};

export const eventByCategoryFilterTypeSlice = createSlice({
    name: 'eventByCategoryFilterType',
    initialState,
    reducers: {
        setEventByCategoryFilterType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventByCategoryFilterType } = eventByCategoryFilterTypeSlice.actions;

export default eventByCategoryFilterTypeSlice.reducer;
