import {configureStore} from '@reduxjs/toolkit';
import {setupListeners} from '@reduxjs/toolkit/query';
import {eluxAPI} from '../API/apiSlice';
import eventbyLocationTimelineMonthReducer from './Slice/EventByLocation/eventByLocationTimelineMonth';
import eventByLocationTimelineYearsReducer from './Slice/EventByLocation/eventByLocationTimelineYear';
import eventByLocationFilterTypeReducer from './Slice/EventByLocation/eventByYearFilterType';
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
        eventbyYearTimelineMonth: eventbyYearTimelineMonthReducer,
        eventByYearFilterType: eventByYearFilterTypeReducer,
        eventByLocationFilterType: eventByLocationFilterTypeReducer,
        eventByLocationTimelineMonths: eventbyLocationTimelineMonthReducer,
        eventByLocationTimelineYears: eventByLocationTimelineYearsReducer,
    },
    middleware: getDefaultMiddleware => getDefaultMiddleware().concat(eluxAPI.middleware),
});

setupListeners(store.dispatch);
