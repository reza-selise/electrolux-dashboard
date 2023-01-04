import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: '',
};

export const graphURLSlice = createSlice({
    name: 'graphURL',
    initialState,
    reducers: {
        setGraphURL: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setGraphURL } = graphURLSlice.actions;

export default graphURLSlice.reducer;
