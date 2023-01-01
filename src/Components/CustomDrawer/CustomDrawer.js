import { Drawer, Select } from 'antd';
import React, { useState } from 'react';
import './CustomDrawer.scss';
const options = ['All', 'ELUX', 'B2B', 'B2C'];

function CustomDrawer({ onClose, open }) {
    const [selectedItems, setSelectedItems] = useState([]);
    const filteredOptions = options.filter(o => !selectedItems.includes(o));
    return (
        <Drawer title="Filters" placement="right" onClose={onClose} open={open}>
            <div className="filter-type-options">
                <Select
                    mode="tags"
                    placeholder="Customer Type"
                    value={selectedItems}
                    onChange={setSelectedItems}
                    style={{
                        width: '100%',
                    }}
                    options={filteredOptions.map(item => ({
                        value: item,
                        label: item,
                    }))}
                />
            </div>
        </Drawer>
    );
}

export default CustomDrawer;
