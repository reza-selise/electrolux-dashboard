import { Select } from 'antd';
import React from 'react';
import ModalButton from '../ModalButton/ModalButton';
import './LocalFilter.scss';

function LocalFilter({ location, setRequestData, showBoth }) {
    const currentYear = new Date().getFullYear();

    const handleYearChange = value => {
        setRequestData(value);
    };

    return (
        <>
            <div className="timeline-button-wrapper">
                <ModalButton location={location}>
                    {currentYear - 5} -{currentYear}
                </ModalButton>
            </div>

            {showBoth === 'true' ? (
                <Select
                    defaultValue="select_type"
                    style={{ width: 120 }}
                    onChange={handleYearChange}
                    options={[
                        {
                            value: 'select_type',
                            label: 'Select Type',
                        },
                        {
                            value: 'events',
                            label: 'Events',
                        },
                        {
                            value: 'participants',
                            label: 'Participants',
                        },
                    ]}
                />
            ) : (
                ''
            )}
        </>
    );
}

export default LocalFilter;
