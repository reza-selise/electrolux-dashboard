import { Drawer } from 'antd';
import React from 'react';

function CustomDrawer({ onClose, open }) {
    return (
        <Drawer title="Filters" placement="right" onClose={onClose} open={open}>
            <p>Some contents...</p>
            <p>Some contents...</p>
            <p>Some contents...</p>
        </Drawer>
    );
}

export default CustomDrawer;
