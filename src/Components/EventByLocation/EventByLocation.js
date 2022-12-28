// eslint-disable-next-line import/no-unresolved
import { useSelector } from 'react-redux';
import { useEventByLocationQuery } from '../../API/apiSlice';
import './EventByLocation.scss';

function EventByLocation() {
    const eventByLocationFilterType = useSelector(state => state.eventByLocationFilterType.value);
    const eventByLocationTimelineYears = useSelector(
        state => state.eventByLocationTimelineYears.value
    );
    const eventbyLocationTimelineMonth = useSelector(
        state => state.eventbyLocationTimelineMonth.value
    );
    const requestBody = eventByLocationTimelineYears.map(year => ({
        year: year.toString(),
        months: eventbyLocationTimelineMonth.toString(),
    }));

    const payload = {
        filter_type: eventByLocationFilterType,
        request_data: 'events',
        locations: '188,191',
        request_body: JSON.stringify(requestBody),
    };
    const { data, error, isLoading } = useEventByLocationQuery(payload);
    console.log(data, error, isLoading);

    return 'Event by location';
}

export default EventByLocation;
