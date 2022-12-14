import { Select } from 'antd';
import React from 'react';
import './LocalFilter.scss';

function LocalFilter({ requestData, setRequestData }) {
    // const currentYear = new Date().getFullYear();

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
        <div>
            <Select
                defaultValue={requestData}
                style={{ width: 200, height: 54 }}
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
        </div>
    );
}

export default LocalFilter;
