import { configureStore } from '@reduxjs/toolkit';
import { setupListeners } from '@reduxjs/toolkit/query';
import { eluxAPI } from '../API/apiSlice';
import locationReducer from './Slice/locationSlice';
import modalReducer from './Slice/modalSlice';

export const store = configureStore({
    reducer: {
        [eluxAPI.reducerPath]: eluxAPI.reducer,
        modal: modalReducer,
        location: locationReducer,
    },
    middleware: (getDefaultMiddleware) => getDefaultMiddleware().concat(eluxAPI.middleware),
});

setupListeners(store.dispatch);
