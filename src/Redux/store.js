import { configureStore } from '@reduxjs/toolkit';
import { setupListeners } from '@reduxjs/toolkit/query';
import { eluxAPI } from '../API/apiSlice';
import collapseReducer from './Slice/collapseSlice';
import modalReducer from './Slice/modalSlice';

export const store = configureStore({
    reducer: {
        [eluxAPI.reducerPath]: eluxAPI.reducer,
        modal: modalReducer,
        collapse: collapseReducer,
    },
    middleware: (getDefaultMiddleware) => getDefaultMiddleware().concat(eluxAPI.middleware),
});

setupListeners(store.dispatch);
