import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [
        'events',
        'participants',
    ],
};

export const typeOfDataSlice = createSlice({
    name: 'typeOfData',
    initialState,
    reducers: {
        setTypeOfData: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setTypeOfData } = typeOfDataSlice.actions;

export default typeOfDataSlice.reducer;
