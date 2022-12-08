import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: false,
};

export const collapseSlice = createSlice({
    name: 'collapse',
    initialState,
    reducers: {
        setCollapse: (state) => {
            state.value = !state.value;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setCollapse } = collapseSlice.actions;

export default collapseSlice.reducer;
