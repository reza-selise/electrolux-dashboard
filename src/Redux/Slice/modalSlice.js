import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: false,
};

export const modalSlice = createSlice({
    name: 'modal',
    initialState,
    reducers: {
        setModal: (state) => {
            state.value = !state.value;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setModal } = modalSlice.actions;

export default modalSlice.reducer;
