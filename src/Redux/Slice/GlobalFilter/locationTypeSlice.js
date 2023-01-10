import { createSlice } from '@reduxjs/toolkit';

const { locations } = window.eluxDashboard.eventGenericFilterData;

const initialState = {
    value: locations.map(({ id }) => id),
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
