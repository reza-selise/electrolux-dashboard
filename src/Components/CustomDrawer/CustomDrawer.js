import { Button, Drawer, Select } from 'antd';
import React, { useState } from 'react';
import { useDispatch } from 'react-redux';
import { setCustomerType } from '../../Redux/Slice/GlobalFilter/customerTypeSlice';
import { setEventStatusType } from '../../Redux/Slice/GlobalFilter/eventStatusTypeSlice';
import { setFbLeadType } from '../../Redux/Slice/GlobalFilter/fbLeadTypeSlice';
import { setLocationType } from '../../Redux/Slice/GlobalFilter/locationTypeSlice';
import { setMainCategorytype } from '../../Redux/Slice/GlobalFilter/mainCategoryTypeSlice';
import { setTypeOfData } from '../../Redux/Slice/GlobalFilter/typeOfDataSlice';
import ModalButton from '../ModalButton/ModalButton';
import './CustomDrawer.scss';

const customerOptions = window.eluxDashboard.eventGenericFilterData.customer_types;

const eventStatusOptions = [
    {
        label: 'Planned',
        value: 'planned',
    },
    {
        label: 'Canceled',
        value: 'canceled',
    },
    {
        label: 'Taken Place',
        value: 'takenPlace',
    },
];
const dataTypeOptions = [
    {
        label: 'Events',
        value: 'events',
    },
    {
        label: 'Participants',
        value: 'participants',
    },
];

const locationOptions = window.eluxDashboard.eventGenericFilterData.locations.map(
    ({ id, name }) => ({ value: id, label: name })
);

const mainCategoryOptions = [
    {
        label: 'Steamdemo ProfiLine',
        value: 'steamdemoProfiline',
    },
    {
        label: 'Steamdemo owners',
        value: 'steamdemoOwners',
    },
    {
        label: 'Steamdemo interested parties',
        value: 'steamdemoInterestedParties',
    },
    {
        label: 'Cooking Course',
        value: 'cookingCourse',
    },
    {
        label: 'Event Location',
        value: 'eventLocation',
    },
    {
        label: 'Diverses',
        value: 'diverses',
    },
    {
        label: 'Special Operations Inhouse',
        value: 'specialOperationsInhouse',
    },
    {
        label: 'Electrolux Employee Training',
        value: 'electroluxEmployeeTraining',
    },
    {
        label: 'AD Customer Event',
        value: 'adCustomerEvent',
    },
];
const fbLeadOptions = [
    {
        label: 'Culinary Ambassadors',
        value: 'culinaryAmbassadors',
    },
    {
        label: 'Consultants',
        value: 'consultants',
    },
    {
        label: 'Admins',
        value: 'admins',
    },
];

function CustomDrawer({ onClose, open }) {
    const [customerOptionsType, setCustomerOptionsType] = useState(
        customerOptions.map(({ value }) => value)
    );
    const [locationOptionsType, setLocationOptionsType] = useState(
        locationOptions.map(({ value }) => value)
    );
    const [mainCategoryOptionsType, setMainCategoryOptionstype] = useState('');
    const [fbLeadOptionsType, setFbLeadOptionsType] = useState('');
    const [typeOfOptionsData, setTypeOfOptionsData] = useState('events');
    const [eventStatusOptionsType, setEventStatusOptionsType] = useState('takenPlace');

    const dispatch = useDispatch();
    // filter
    const handleCustomerTypeChange = value => {
        setCustomerOptionsType(value);
    };
    const handleLocationTypeChange = value => {
        setLocationOptionsType(value);
    };
    const handleMainCategoryTypeChange = value => {
        setMainCategoryOptionstype(value);
    };
    const handleFbLeadTypeChange = value => {
        setFbLeadOptionsType(value);
    };
    const handleTypeOfDataChange = value => {
        setTypeOfOptionsData(value);
    };
    const handleEventStatusTypeChange = value => {
        setEventStatusOptionsType(value);
    };

    // dispatch all filter for the global state here.
    const applyFilterBtn = () => {
        dispatch(setCustomerType(customerOptionsType));
        dispatch(setLocationType(locationOptionsType));
        dispatch(setMainCategorytype(mainCategoryOptionsType));
        dispatch(setFbLeadType(fbLeadOptionsType));
        dispatch(setTypeOfData(typeOfOptionsData));
        dispatch(setEventStatusType(eventStatusOptionsType));
        console.log('button clicked');
    };

    return (
        <Drawer title="Filters" placement="right" onClose={onClose} open={open}>
            <div className="filter-type-options">
                <Select
                    defaultValue={customerOptionsType}
                    mode="multiple"
                    placeholder="Customer Type"
                    onChange={handleCustomerTypeChange}
                    style={{
                        width: '100%',
                    }}
                    options={customerOptions}
                />
                <Select
                    defaultValue={locationOptions.map(({ value }) => value)}
                    mode="multiple"
                    placeholder="Location"
                    onChange={handleLocationTypeChange}
                    style={{
                        width: '100%',
                    }}
                    options={locationOptions}
                />
                <Select
                    className="single-select-box"
                    placeholder="Main Category"
                    onChange={handleMainCategoryTypeChange}
                    style={{
                        width: '100%',
                    }}
                    options={mainCategoryOptions}
                />
                <Select
                    mode="multiple"
                    placeholder="FB Lead"
                    onChange={handleFbLeadTypeChange}
                    style={{
                        width: '100%',
                    }}
                    options={fbLeadOptions}
                />

                <Select
                    defaultValue="Events"
                    className="single-select-box"
                    placeholder="Type Of Data"
                    onChange={handleTypeOfDataChange}
                    style={{
                        width: '100%',
                    }}
                    options={dataTypeOptions}
                />
                <Select
                    defaultValue="Taken Place"
                    placeholder="Event Status"
                    onChange={handleEventStatusTypeChange}
                    style={{
                        width: '100%',
                    }}
                    options={eventStatusOptions}
                />
                <div className="generic-timeline-button-wrapper">
                    <ModalButton location="global-timeline">Timeline</ModalButton>
                    <div className="apply-filter-button">
                        <Button onClick={applyFilterBtn}>Apply Filter</Button>
                    </div>
                </div>
            </div>
        </Drawer>
    );
}

export default CustomDrawer;
