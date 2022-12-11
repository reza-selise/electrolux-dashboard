import { Layout } from 'antd';
import React, { StrictMode } from 'react';
import ReactDOM from 'react-dom/client';
import { Provider } from 'react-redux';
import Events from './Pages/Events/Events';
import { store } from './Redux/store';

if (document.getElementById('eventDashboard') != null) {
    const eventDashboard = ReactDOM.createRoot(document.getElementById('eventDashboard'));
    eventDashboard.render(
        <StrictMode>
            <Provider store={store}>
                <Layout style={{ paddingRight: '20px' }}>
                    <Events />
                </Layout>
            </Provider>
        </StrictMode>
    );
}
