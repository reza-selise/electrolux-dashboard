import React from 'react';
import { useGetEventByDateQuery } from '../../API/apiSlice';
import Container from '../../Components/Container/Container';
import CustomModal from '../../Components/CustomModal/CustomModal';
import GlobalFilterButton from '../../Components/GlobalFilterButton/GlobalFilterButton';
import { eluxTranslation } from '../../Translation/Translation';
import './Events.scss';

function Events() {
    const { data, error, isLoading } = useGetEventByDateQuery('bulbasaur');
    const { eventDashboard } = eluxTranslation;
    return (
        <>
            <h2 className="section-title">{eventDashboard}</h2>
            <Container>
                <div className="events">
                    {error ? (
                        <>Oh no, there was an error</>
                    ) : isLoading ? (
                        <>Loading...</>
                    ) : data ? (
                        <>
                            <h3>{data.species.name}</h3>
                            <img src={data.sprites.front_shiny} alt={data.species.name} />
                        </>
                    ) : null}
                </div>
            </Container>
            <GlobalFilterButton />
            <CustomModal />
        </>
    );
}

export default Events;
