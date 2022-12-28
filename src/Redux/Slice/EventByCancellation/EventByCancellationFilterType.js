import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: 'years',
};

export const eventByCancellationFilterTypeSlice = createSlice({
    name: 'eventByCancellationFilterType',
    initialState,
    reducers: {
        setEventByCancellationFilterType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setEventByCancellationFilterType } = eventByCancellationFilterTypeSlice.actions;

export default eventByCancellationFilterTypeSlice.reducer;
