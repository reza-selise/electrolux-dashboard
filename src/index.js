import React, { StrictMode } from 'react';
import ReactDOM from 'react-dom/client';
import App from './App/App';

const eventDashboard = ReactDOM.createRoot(document.getElementById('eventDashboard'));
if(eventDashboard.length > 0){
    eventDashboard.render(
        <StrictMode>
            <App />
        </StrictMode>
    );
}

