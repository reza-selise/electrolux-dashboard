import { Menu } from 'antd';
import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import dashboardIcon from '../../images/dashboard-icon.svg';
import './CustomMenu.scss';

const menuItems = [
    {
        label: 'Dashboard',
        key: 'dashboard',
        icon: <img src={dashboardIcon} alt="dashboard icon" />,
        children: [
            {
                key: 'events',
                label: 'Events',
            },
            {
                key: 'consultation',
                // icon: React.createElement(icon),
                label: 'Consultation',
            },
            {
                key: 'compact-table',
                // icon: React.createElement(icon),
                label: 'Compact Table',
            },
            {
                key: 'home-consultation',
                // icon: React.createElement(icon),
                label: 'Home Consultation',
            },
            {
                key: 'management-dashboard',
                // icon: React.createElement(icon),
                label: 'Management Dashboard',
            },
        ],
    },
];

function CustomMenu() {
    const location = useLocation();
    const navigate = useNavigate();
    const [current, setCurrent] = useState(
        location.pathname === '/' || location.pathname === ''
            ? 'events'
            : location.pathname.split('/')[1]
    );

    const handleMenuClick = (event) => {
        if (event.key === 'events') {
            navigate('/');
        } else {
            navigate(`/${event.key}`);
        }
        setCurrent(event.key);
    };
    return (
        <Menu
            onClick={handleMenuClick}
            defaultSelectedKeys={[current]}
            mode="inline"
            items={menuItems}
        />
    );
}

export default CustomMenu;
