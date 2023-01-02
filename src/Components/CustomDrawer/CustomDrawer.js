import { Drawer, Select } from 'antd';
import React, { useState } from 'react';
import './CustomDrawer.scss';
const customerOptions = ['All', 'ELUX', 'B2B', 'B2C'];
const locationOptions = [
    'Bern',
    'Zurich',
    'St. Gallen',
    'Chur',
    'Charrant',
    'Preverenges',
    'Manno',
    'Kriens',
    'Pratteln',
    'Magenwil',
    'Volketswil',
];

const mainCategoryOptions = [
    'Steamdemo ProfiLine',
    'Steamdemo owners',
    'Steamdemo interested parties',
    'Cooking Course',
    'Event Location',
    'Diverses',
    'Special Operations Inhouse',
    'Electrolux Employee Training',
    'AD Customer Event',
];

const fbLeadOptions = ['Culinary Ambassadors','Consultants','Admins']

function CustomDrawer({ onClose, open }) {
    const [customerSelectedItems, setCustomerSelectedItems] = useState([]);
    const [location, setLocation] = useState([]);
    const [mainCategory, setMainCategory] = useState([]);
    const [fbLead, setFbLead]=useState([])

    const customerFilteredOptions = customerOptions.filter(o => !customerSelectedItems.includes(o));
    const locationFilteredOptions = locationOptions.filter(o => !location.includes(o));
    const mainCategoryFilteredOptions = mainCategoryOptions.filter(o => !mainCategory.includes(o));
    const fbLeadFilteredOptions = fbLeadOptions.filter(o => !fbLead.includes(o));


    return (
        <Drawer title="Filters" placement="right" onClose={onClose} open={open}>
            <div className="filter-type-options">
                <Select
                    mode="tags"
                    placeholder="Customer Type"
                    value={customerSelectedItems}
                    onChange={setCustomerSelectedItems}
                    style={{
                        width: '100%',
                    }}
                    options={customerFilteredOptions.map(item => ({
                        value: item,
                        label: item,
                    }))}
                />
                <Select
                    mode="tags"
                    placeholder="Location"
                    value={location}
                    onChange={setLocation}
                    style={{
                        width: '100%',
                    }}
                    options={locationFilteredOptions.map(item => ({
                        value: item,
                        label: item,
                    }))}
                />
                <Select
                    mode="tags"
                    placeholder="Main Category"
                    value={mainCategory}
                    onChange={setMainCategory}
                    style={{
                        width: '100%',
                    }}
                    options={mainCategoryFilteredOptions.map(item => ({
                        value: item,
                        label: item,
                    }))}
                />
                 <Select
                    mode="tags"
                    placeholder="FB Lead"
                    value={fbLead}
                    onChange={setFbLead}
                    style={{
                        width: '100%',
                    }}
                    options={fbLeadFilteredOptions.map(item => ({
                        value: item,
                        label: item,
                    }))}
                />
            </div>
        </Drawer>
    );
}

export default CustomDrawer;
