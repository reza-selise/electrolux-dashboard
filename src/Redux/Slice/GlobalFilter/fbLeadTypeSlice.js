import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    value: [
        'culinaryAmbassadors',
        'consultants',
        'admins',
    ],
};

export const fbLeadTypeSlice = createSlice({
    name: 'fbLeadType',
    initialState,
    reducers: {
        setFbLeadType: (state, action) => {
            state.value = action.payload;
        },
    },
});

// Action creators are generated for each case reducer function
export const { setFbLeadType } = fbLeadTypeSlice.actions;

export default fbLeadTypeSlice.reducer;
