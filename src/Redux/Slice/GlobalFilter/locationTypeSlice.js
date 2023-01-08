import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [
        'bern',
        'zurich',
        'st_gallen',
        'chur',
        'charrant',
        'preverenges',
        'manno',
        'kriens',
        'pratteln',
        'magenwil',
        'volketswil',
    ],
};

export const locationTypeSlice = createSlice({
    name: 'locationType',
    initialState,
    reducers: {
        setLocationType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setLocationType } = locationTypeSlice.actions;

export default locationTypeSlice.reducer;