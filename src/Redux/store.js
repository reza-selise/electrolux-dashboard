import { configureStore } from '@reduxjs/toolkit';
import { setupListeners } from '@reduxjs/toolkit/query';
import { eluxAPI } from '../API/apiSlice';
import cookingCourseCustomDateReducer from './Slice/CookingCourseType/CookingCourseCustomDate';
import cookingCourseFilterTypeReducer from './Slice/CookingCourseType/CookingCourseFilterType';
import cookingCourseMonthsReducer from './Slice/CookingCourseType/CookingCourseMonths';
import cookingCourseYearMonthsReducer from './Slice/CookingCourseType/CookingCourseYearMonths';
import cookingCourseYearsReducer from './Slice/CookingCourseType/CookingCourseYears';
import eventByCancellationCustomDateReducer from './Slice/EventByCancellation/EventByCancellationCustomDate';
import eventByCancellationFilterTypeReducer from './Slice/EventByCancellation/EventByCancellationFilterType';
import eventByCancellationMonthsReducer from './Slice/EventByCancellation/EventByCancellationMonths';
import eventByCancellationYearMonthsReducer from './Slice/EventByCancellation/EventByCancellationYearMonths';
import eventByCancellationYearsReducer from './Slice/EventByCancellation/EventByCancellationYears';
import eventByCategoryFilterTypeReducer from './Slice/EventByCategory/eventByCategoryFilterType';
import eventbyCategoryTimelineYearsReducer from './Slice/EventByCategory/eventbyCategoryTimelineYears';
import eventByLocationFilterTypeReducer from './Slice/EventByLocation/eventByLocationFilterType';
import eventbyLocationTimelineMonthReducer from './Slice/EventByLocation/eventByLocationTimelineMonth';
import eventByLocationTimelineYearsReducer from './Slice/EventByLocation/eventByLocationTimelineYear';
import eventByMonthFilterTypeReducer from './Slice/EventByMonth/eventByMonthFilterType';
import eventbyMonthTimelineYearsReducer from './Slice/EventByMonth/eventByMonthTimelineYears';
import eventbyMonthTimelineMonthReducer from './Slice/EventByMonth/eventMyMonthTimelineMonth';
import eventByStatusCustomDateReducer from './Slice/EventByStatus/EventByStatusCustomDate';
import eventByStatusFilterTypeReducer from './Slice/EventByStatus/EventByStatusFilterType';
import eventByStatusMonthsReducer from './Slice/EventByStatus/EventByStatusMonths';
import eventByStatusYearMonthReducer from './Slice/EventByStatus/EventByStatusYearMonth';
import eventByStatusYearsReducer from './Slice/EventByStatus/EventByStatusYears';
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
        eventByCancellationFilterType:eventByCancellationFilterTypeReducer,
        eventByCancellationYears:eventByCancellationYearsReducer,
        eventByCancellationMonths:eventByCancellationMonthsReducer,
        eventByCancellationYearMonths:eventByCancellationYearMonthsReducer,
        eventByCancellationCustomDate:eventByCancellationCustomDateReducer,
        eventByStatusFilterType: eventByStatusFilterTypeReducer,
        eventByStatusYearMonth: eventByStatusYearMonthReducer,
        eventByStatusYears: eventByStatusYearsReducer,
        eventByStatusMonths: eventByStatusMonthsReducer,
        eventByStatusCustomDate: eventByStatusCustomDateReducer,
    },
    middleware: getDefaultMiddleware => getDefaultMiddleware().concat(eluxAPI.middleware),
});

setupListeners(store.dispatch);
