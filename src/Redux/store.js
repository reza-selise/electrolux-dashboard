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
import eventByLocationTimelineYearsReducer from './Slice/EventByLocation/eventByLocationTimelineYears';
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
import eventPerSalesPersonFilterTypeReducer from './Slice/EventPerSalesPerson/EventPerSalesPersonFilterType';
import customerTypeReducer from './Slice/GlobalFilter/customerTypeSlice';
import eventStatusTypeReducer from './Slice/GlobalFilter/eventStatusTypeSlice';
import fbLeadTypeReducer from './Slice/GlobalFilter/fbLeadTypeSlice';
import locationTypeReducer from './Slice/GlobalFilter/locationTypeSlice';
import mainCategoryTypeReducer from './Slice/GlobalFilter/mainCategoryTypeSlice';
import subCategoryTypeReducer from './Slice/GlobalFilter/subCategoryTypeSlice';
import typeOfDataReducer from './Slice/GlobalFilter/typeOfDataSlice';
import globalFilterTimelineCustomDateReducer from './Slice/GlobalFilterTimeline/GlobalFilterTimelineCustomDate';
import globalFilterTimelineFilterTypeReducer from './Slice/GlobalFilterTimeline/GlobalFilterTimelineFilterType';
import globalFilterTimelineMonthsReducer from './Slice/GlobalFilterTimeline/GlobalFilterTimelineMonths';
import globalFilterTimelineYearMonthsReducer from './Slice/GlobalFilterTimeline/GlobalFilterTimelineYearMonths';
import globalFilterTimelineYearsReducer from './Slice/GlobalFilterTimeline/GlobalFilterTimelineYears';
import graphURLReducer from './Slice/graphURL';
import tableURLReducer from './Slice/tableURL';

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
        eventByCancellationFilterType: eventByCancellationFilterTypeReducer,
        eventByCancellationYears: eventByCancellationYearsReducer,
        eventByCancellationMonths: eventByCancellationMonthsReducer,
        eventByCancellationYearMonths: eventByCancellationYearMonthsReducer,
        eventByCancellationCustomDate: eventByCancellationCustomDateReducer,
        eventByStatusFilterType: eventByStatusFilterTypeReducer,
        eventByStatusYearMonth: eventByStatusYearMonthReducer,
        eventByStatusYears: eventByStatusYearsReducer,
        eventByStatusMonths: eventByStatusMonthsReducer,
        eventByStatusCustomDate: eventByStatusCustomDateReducer,
        eventPerSalesPersonFilterType: eventPerSalesPersonFilterTypeReducer,
        graphURL: graphURLReducer,
        globalFilterTimelineFilterType: globalFilterTimelineFilterTypeReducer,
        globalFilterTimelineYears: globalFilterTimelineYearsReducer,
        globalFilterTimelineMonths: globalFilterTimelineMonthsReducer,
        globalFilterTimelineYearMonths: globalFilterTimelineYearMonthsReducer,
        globalFilterTimelineCustomDate: globalFilterTimelineCustomDateReducer,
        customerType: customerTypeReducer,
        locationType: locationTypeReducer,
        mainCategoryType: mainCategoryTypeReducer,
        subCategoryType: subCategoryTypeReducer,
        fbLeadType: fbLeadTypeReducer,
        typeOfData: typeOfDataReducer,
        eventStatusType: eventStatusTypeReducer,
        tableURL: tableURLReducer,
    },
    middleware: getDefaultMiddleware => getDefaultMiddleware().concat(eluxAPI.middleware),
});

setupListeners(store.dispatch);
