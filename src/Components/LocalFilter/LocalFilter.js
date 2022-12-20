import { Select } from 'antd';
import React from 'react';
import ModalButton from '../ModalButton/ModalButton';
import './LocalFilter.scss';

function LocalFilter({ location, requestData, setRequestData }) {
    // const dispatch = useDispatch();
    const currentYear = new Date().getFullYear();

    // const [years, setYears] = useState([]);

    // useEffect(() => {
    //     const years = [];
    //     for (let year = 1950; year <= currentYear; year += 1) {
    //         const yearObject = {
    //             label: year,
    //             value: year,
    //         };

    //         years.push(yearObject);
    //         setYears(years);
    //     }
    // }, []);
    const handleYearChange = (value) => {
        setRequestData(value);
    };

    return (
        <>
            <div className="timeline-button-wrapper">
                <ModalButton location={location}>
                    {currentYear - 5} -{currentYear}
                </ModalButton>
            </div>
            <Select
                defaultValue={requestData}
                style={{ width: 120 }}
                onChange={handleYearChange}
                options={[
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
        </>
    );
}

export default LocalFilter;
