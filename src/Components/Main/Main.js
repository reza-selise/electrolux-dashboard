import { Layout } from 'antd';
import React from 'react';
import { useSelector } from 'react-redux';
import { Route, Routes } from 'react-router-dom';
import CompactTable from '../../Pages/CompactTable/CompactTable';
import Consultation from '../../Pages/Consultation/Consultation';
import Events from '../../Pages/Events/Events';
import HomeConsultation from '../../Pages/HomeConsultation/HomeConsultation';
import ManagementDashboard from '../../Pages/ManagementDashboard/ManagementDashboard';
import './Main.scss';

const { Content } = Layout;
function Main() {
    const collapsed = useSelector((state) => state.collapse.value);

    return (
        <Content
            className="site-content"
            style={{
                marginLeft: !collapsed ? 292 : 40,
            }}
        >
            <Routes>
                <Route path="/" element={<Events />} />
                <Route path="/consultation" element={<Consultation />} />
                <Route path="/compact-table" element={<CompactTable />} />
                <Route path="/home-consultation" element={<HomeConsultation />} />
                <Route path="/management-dashboard" element={<ManagementDashboard />} />
            </Routes>
        </Content>
    );
}

export default Main;
