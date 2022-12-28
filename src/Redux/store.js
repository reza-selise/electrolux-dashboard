import { configureStore } from '@reduxjs/toolkit';
import { setupListeners } from '@reduxjs/toolkit/query';
import { eluxAPI } from '../API/apiSlice';
import cookingCourseCustomDateReducer from './Slice/CookingCourseType/CookingCourseCustomDate';
import cookingCourseFilterTypeReducer from './Slice/CookingCourseType/CookingCourseFilterType';
import cookingCourseMonthsReducer from './Slice/CookingCourseType/CookingCourseMonths';
import cookingCourseYearMonthsReducer from './Slice/CookingCourseType/CookingCourseYearMonths';
import cookingCourseYearsReducer from './Slice/CookingCourseType/CookingCourseYears';
import eventByCategoryFilterTypeReducer from './Slice/EventByCategory/eventByCategoryFilterType';
import eventbyCategoryTimelineYearsReducer from './Slice/EventByCategory/eventbyCategoryTimelineYears';
import eventByLocationFilterTypeReducer from './Slice/EventByLocation/eventByLocationFilterType';
import eventbyLocationTimelineMonthReducer from './Slice/EventByLocation/eventByLocationTimelineMonth';
import eventByLocationTimelineYearsReducer from './Slice/EventByLocation/eventByLocationTimelineYear';
import eventByMonthFilterTypeReducer from './Slice/EventByMonth/eventByMonthFilterType';
import eventbyMonthTimelineYearsReducer from './Slice/EventByMonth/eventByMonthTimelineYears';
import eventbyMonthTimelineMonthReducer from './Slice/EventByMonth/eventMyMonthTimelineMonth';
import eventByStatusFilterTypeReducer from './Slice/EventByStatus/EventByStatusFilterType';
import eventByYearFilterTypeReducer from './Slice/EventByYear/eventByYearFilterType';
import eventbyYearTimelineMonthReducer from './Slice/EventByYear/eventByYearTimelineMonth';
import eventbyYearTimelineYearsReducer from './Slice/EventByYear/eventByYearTimelineYear';
import eventbyYearTimelineYearDateRangeReducer from './Slice/EventByYear/eventbyYearTimelineYearDateRange';
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
        eventbyMonthTimelineMonth: eventbyMonthTimelineMonthReducer,
        eventbyMonthTimelineYears: eventbyMonthTimelineYearsReducer,
        eventByYearFilterType: eventByYearFilterTypeReducer,
        eventByMonthFilterType: eventByMonthFilterTypeReducer,
        eventByCategoryFilterType: eventByCategoryFilterTypeReducer,
        eventbyYearTimelineYearDateRange: eventbyYearTimelineYearDateRangeReducer,
        cookingCourseFilterType: cookingCourseFilterTypeReducer,
        cookingCourseYears: cookingCourseYearsReducer,
        cookingCourseMonths: cookingCourseMonthsReducer,
        cookingCourseYearMonths: cookingCourseYearMonthsReducer,
        cookingCourseCustomDate: cookingCourseCustomDateReducer,
        eventByLocationFilterType: eventByLocationFilterTypeReducer,
        eventbyLocationTimelineMonth: eventbyLocationTimelineMonthReducer,
        eventByLocationTimelineYears: eventByLocationTimelineYearsReducer,
        eventByStatusFilterType: eventByStatusFilterTypeReducer,
    },
    middleware: getDefaultMiddleware => getDefaultMiddleware().concat(eluxAPI.middleware),
});

setupListeners(store.dispatch);
