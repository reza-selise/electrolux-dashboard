import { createSlice } from '@reduxjs/toolkit';

const fbLeadOptions = window.eluxDashboard.eventGenericFilterData.fb_leads.map(role => ({
    label: role.role_name,
    options: role.users,
}));
const initialState = {
    value: fbLeadOptions.map(role => role.options.map(options => options.value)).flat(),
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
