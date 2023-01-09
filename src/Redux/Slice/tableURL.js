import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: '',
};

export const tableURLSlice = createSlice({
    name: 'tableURL',
    initialState,
    reducers: {
        setTableURL: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setTableURL } = tableURLSlice.actions;

export default tableURLSlice.reducer;
