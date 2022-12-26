import { configureStore } from '@reduxjs/toolkit';
import { setupListeners } from '@reduxjs/toolkit/query';
import { eluxAPI } from '../API/apiSlice';
import eventByCategoryFilterTypeReducer from './Slice/EventByCategory/eventByCategoryFilterType';
import eventbyCategoryTimelineYearsReducer from './Slice/EventByCategory/eventbyCategoryTimelineYears';
import eventByMonthFilterTypeReducer from './Slice/EventByMonth/eventByMonthFilterType';
import eventbyMonthTimelineYearsReducer from './Slice/EventByMonth/eventByMonthTimelineYears';
import eventbyMonthTimelineMonthReducer from './Slice/EventByMonth/eventMyMonthTimelineMonth';
import eventByYearFilterTypeReducer from './Slice/EventByYear/eventByYearFilterType';
import eventbyYearTimelineMonthReducer from './Slice/EventByYear/eventByYearTimelineMonth';
import eventbyYearTimelineYearsReducer from './Slice/EventByYear/eventByYearTimelineYear';
import locationReducer from './Slice/locationSlice';
import modalReducer from './Slice/modalSlice';

export const store = configureStore({
    reducer: {
        [eluxAPI.reducerPath]: eluxAPI.reducer,
        modal: modalReducer,
        location: locationReducer,
        eventbyYearTimelineYears: eventbyYearTimelineYearsReducer,
        eventbyCategoryTimelineYears: eventbyCategoryTimelineYearsReducer,
        eventbyYearTimelineMonth: eventbyYearTimelineMonthReducer,
        eventbyMonthTimelineMonth :eventbyMonthTimelineMonthReducer, 
        eventbyMonthTimelineYears: eventbyMonthTimelineYearsReducer,
        eventByYearFilterType: eventByYearFilterTypeReducer,
        eventByMonthFilterType: eventByMonthFilterTypeReducer,
        eventByCategoryFilterType: eventByCategoryFilterTypeReducer,
    },
    middleware: getDefaultMiddleware => getDefaultMiddleware().concat(eluxAPI.middleware),
});

setupListeners(store.dispatch);
