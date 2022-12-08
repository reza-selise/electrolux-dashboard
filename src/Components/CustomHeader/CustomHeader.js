import { Layout } from 'antd';
import React from 'react';
import './CustomHeader.scss';

const { Header } = Layout;

function CustomHeader() {
    return (
        <Header
            className="site-header"
            style={{ position: 'sticky', top: 0, zIndex: 1, width: '100%' }}
        />
    );
}

export default CustomHeader;
