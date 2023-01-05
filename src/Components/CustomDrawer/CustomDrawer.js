import { Drawer, Select } from 'antd';
import React, { useState } from 'react';
import { useDispatch } from 'react-redux';
import { setCustomerType } from '../../Redux/Slice/GlobalFilter/customerTypeSlice';
import ModalButton from '../ModalButton/ModalButton';
import './CustomDrawer.scss';

const customerOptions = ['All', 'ELUX', 'B2B', 'B2C'];
const eventStatusOptions = ['Planned', 'Canceled', 'Taken Place'];
const dataTypeOptions = ['Events', 'Participants'];
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
const fbLeadOptions = ['Culinary Ambassadors', 'Consultants', 'Admins'];

function CustomDrawer({ onClose, open }) {
    const [customerSelectedItems, setCustomerSelectedItems] = useState([]);
    const [filterLocation, setFilterLocation] = useState([]);
    const [mainCategory, setMainCategory] = useState([]);
    const [fbLead, setFbLead] = useState([]);
    const [dataType, setDataType] = useState([]);
    const [eventStatus, setEventStatus] = useState([]);

    const dispatch = useDispatch();
    // filter

    const customerFilteredOptions = customerOptions.filter(o => !customerSelectedItems.includes(o));
    const locationFilteredOptions = locationOptions.filter(o => !filterLocation.includes(o));
    const mainCategoryFilteredOptions = mainCategoryOptions.filter(o => !mainCategory.includes(o));
    const fbLeadFilteredOptions = fbLeadOptions.filter(o => !fbLead.includes(o));
    const typeOfDataFilteredOptions = dataTypeOptions.filter(o => !dataType.includes(o));
    const eventStatusFilteredOptions = eventStatusOptions.filter(o => !eventStatus.includes(o));

    const handleCustomerTypeChange = value => {
        setCustomerSelectedItems(value);
    };

    const applyFilterBtn = () => {
        dispatch(setCustomerType(customerSelectedItems));
        // dispatch all filter for the global state here.
        
    };

    return (
        <Drawer title="Filters" placement="right" onClose={onClose} open={open}>
            <div className="filter-type-options">
                <Select
                    mode="tags"
                    placeholder="Customer Type"
                    value={customerSelectedItems}
                    onChange={handleCustomerTypeChange}
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
                    value={filterLocation}
                    onChange={setFilterLocation}
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

                <Select
                    mode="tags"
                    placeholder="Type Of Data"
                    value={dataType}
                    onChange={setDataType}
                    style={{
                        width: '100%',
                    }}
                    options={typeOfDataFilteredOptions.map(item => ({
                        value: item,
                        label: item,
                    }))}
                />
                <Select
                    mode="tags"
                    placeholder="Event Status"
                    value={eventStatus}
                    onChange={setEventStatus}
                    style={{
                        width: '100%',
                    }}
                    options={eventStatusFilteredOptions.map(item => ({
                        value: item,
                        label: item,
                    }))}
                />
                <div className="generic-timeline-button-wrapper">
                    <ModalButton location="global-timeline">Timeline</ModalButton>
                </div>
                <button onClick={applyFilterBtn} type="button">
                    Apply Filter
                </button>
            </div>
        </Drawer>
    );
}

export default CustomDrawer;
