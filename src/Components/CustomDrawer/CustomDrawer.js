import { Button, Drawer, Select } from 'antd';
import React, { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { setCustomerType } from '../../Redux/Slice/GlobalFilter/customerTypeSlice';
import { setEventStatusType } from '../../Redux/Slice/GlobalFilter/eventStatusTypeSlice';
import { setFbLeadType } from '../../Redux/Slice/GlobalFilter/fbLeadTypeSlice';
import { setLocationType } from '../../Redux/Slice/GlobalFilter/locationTypeSlice';
import { setMainCategorytype } from '../../Redux/Slice/GlobalFilter/mainCategoryTypeSlice';
import { setSubCategoryType } from '../../Redux/Slice/GlobalFilter/subCategoryTypeSlice';
import { setTypeOfData } from '../../Redux/Slice/GlobalFilter/typeOfDataSlice';
import ModalButton from '../ModalButton/ModalButton';
import './CustomDrawer.scss';

const customerOptions = window.eluxDashboard.eventGenericFilterData.customer_types;

const eventStatusOptions = window.eluxDashboard.eventGenericFilterData.event_status;

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

const mainCategoryOptions = window.eluxDashboard.eventGenericFilterData.categories.map(
    ({ id, name }) => ({ value: id, label: name })
);

const fbLeadOptions = window.eluxDashboard.eventGenericFilterData.fb_leads.map(role => ({
    label: role.role_name,
    options: role.users,
}));

function CustomDrawer({ onClose, open }) {
    const [customerOptionsType, setCustomerOptionsType] = useState(
        customerOptions.map(({ value }) => value)
    );
    const [locationOptionsType, setLocationOptionsType] = useState(
        locationOptions.map(({ value }) => value)
    );
    const [mainCategoryOptionsType, setMainCategoryOptionstype] = useState(
        mainCategoryOptions.map(({ value }) => value)
    );
    const [subCategoryOptions, setSubCategoryOptions] = useState();
    const [subCategoryValue, setSubCategoryValue] = useState();
    const [fbLeadOptionsType, setFbLeadOptionsType] = useState(
        fbLeadOptions.map(role => role.options.map(options => options.value)).flat()
    );
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

    const handleSubCategory = value => {
        setSubCategoryValue(value);
    };

    // dispatch all filter for the global state here.
    const applyFilterBtn = () => {
        dispatch(setCustomerType(customerOptionsType));
        dispatch(setLocationType(locationOptionsType));
        dispatch(setMainCategorytype(mainCategoryOptionsType));
        dispatch(setFbLeadType(fbLeadOptionsType));
        dispatch(setTypeOfData(typeOfOptionsData));
        dispatch(setEventStatusType(eventStatusOptionsType));
        dispatch(setSubCategoryType(subCategoryValue));
    };

    useEffect(() => {
        const matchCategory = window.eluxDashboard.eventGenericFilterData.categories.filter(
            category => mainCategoryOptionsType.includes(category.id)
        );
        const subCategory = matchCategory.reduce((subCat, category) => {
            subCat.push(...category.sub_category);
            return subCat;
        }, []);

        if (subCategory.length > 0) {
            setSubCategoryOptions(subCategory.map(({ id, name }) => ({ value: id, label: name })));
            setSubCategoryValue(subCategory.map(({ id }) => id));
        } else {
            setSubCategoryOptions();
        }
    }, [mainCategoryOptionsType]);

    

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
                    mode="multiple"
                    defaultValue={mainCategoryOptionsType}
                    className="single-select-box"
                    placeholder="Main Category"
                    onChange={handleMainCategoryTypeChange}
                    style={{
                        width: '100%',
                    }}
                    options={mainCategoryOptions}
                />
                {typeof subCategoryOptions !== 'undefined' && (
                    <Select
                        mode="multiple"
                        defaultValue={subCategoryValue}
                        className="single-select-box"
                        placeholder="Sub Category"
                        onChange={handleSubCategory}
                        style={{
                            width: '100%',
                        }}
                        options={subCategoryOptions}
                    />
                )}
                <Select
                    mode="multiple"
                    placeholder="FB Lead"
                    defaultValue={fbLeadOptionsType}
                    onChange={handleFbLeadTypeChange}
                    style={{
                        width: '100%',
                    }}
                    options={fbLeadOptions}
                />
                <Select
                    defaultValue="events"
                    className="single-select-box"
                    placeholder="Sales Employee(s)"
                    onChange={handleTypeOfDataChange}
                    style={{
                        width: '100%',
                    }}
                    options={dataTypeOptions}
                />
                <Select
                    defaultValue="events"
                    className="single-select-box"
                    placeholder="Type Of Data"
                    onChange={handleTypeOfDataChange}
                    style={{
                        width: '100%',
                    }}
                    options={dataTypeOptions}
                />
                <Select
                    defaultValue="took_place"
                    className="single-select-box"
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
