import { Layout } from 'antd';
import React from 'react';
import { Provider } from 'react-redux';
import { BrowserRouter } from 'react-router-dom';
import CustomHeader from '../Components/CustomHeader/CustomHeader';
import Main from '../Components/Main/Main';
import Sidebar from '../Components/Sidebar/Sidebar';
import { store } from '../Redux/store';
import './App.scss';

function App() {
    return (
        <Provider store={store}>
            <BrowserRouter>
                <Layout>
                    <Sidebar />
                    <Layout>
                        <CustomHeader />
                        <Main />
                    </Layout>
                </Layout>
            </BrowserRouter>
        </Provider>
    );
}

export default App;
